<?php

/**
 * cURL module is needed in order for this class to work
 */
if (!function_exists('curl_init')) {
	throw new Exception('PasteBin library needs the CURL PHP extension.');
}

/**
 * PasteBin API wrapper class. Can be used to create new Paste.
 *
 * @author Marius Barbulescu
 * @version 0.1
 */
class PasteBin
{
	/**
	 * Library version 
	 */
	const VERSION = 0.1;

	/**
	 * API URL for POST requests
	 */
	const API_POST_URL = 'http://pastebin.com/api/api_post.php';

	/**
	 * API params field names
	 */
	const API_FIELD_DEV_KEY  = 'api_dev_key';
	const API_FIELD_USER_KEY = 'api_user_key';
	const API_FIELD_OPTION 	 = 'api_option';	
	const API_FIELD_PASTE_CODE 		  = 'api_paste_code';	
	const API_FIELD_PASTE_PRIVATE 	  = 'api_paste_private';	
	const API_FIELD_PASTE_NAME 		  = 'api_paste_name';	
	const API_FIELD_PASTE_EXPIRE_DATE = 'api_paste_expire_date';	
	const API_FIELD_PASTE_FORMAT 	  = 'api_paste_format';			

	/**
	 * Default API option value when creating a new Paste.
	 */
	const API_OPTION_CREATE = 'paste';

	/**
	 * API paste expiration date values
	 */
	const PASTE_EXPIRE_NEVER = 'N'; // Never
    const PASTE_EXPIRE_10M   = '10M'; // 10 Minutes
    const PASTE_EXPIRE_1H 	 = '1H'; // 1 Hour
    const PASTE_EXPIRE_1D 	 = '1D'; // 1 Day
    const PASTE_EXPIRE_1W 	 = '1W'; // 1 Week
    const PASTE_EXPIRE_2W 	 = '2W'; // 2 Weeks
    const PASTE_EXPIRE_1M 	 = '1M'; // 1 Month

    /**
     * API paste visibility types
     */
    const PASTE_PRIVACY_PUBLIC = '0';
    const PASTE_PRIVACY_UNLISTED = '1';
    const PASTE_PRIVACY_PRIVATE = '2';

    /**
     * API Developer Key needed in order to make API requests
     * @var string
     */
    public $apiDevKey;

    /**
     * API error, in case there is one
     * 
     * @var string
     */
    public $apiError;

    /**
     * cURL request error, if any
     * 
     * @var string
     */
    public $curlError;

    /**
     * API paste visibility type => label array
     * 
     * @var array
     */
    public static $pastePrivacyTypes = array(
    	self::PASTE_PRIVACY_PUBLIC => 'Public',
    	self::PASTE_PRIVACY_UNLISTED => 'Unlisted',
    	self::PASTE_PRIVACY_PRIVATE => 'Private',
    ); 

    /**
     * API paste expire date type => label array
     * 
     * @var array
     */
    public static $pasteExpireTypes = array(
    	self::PASTE_EXPIRE_NEVER => 'Never',
	    self::PASTE_EXPIRE_10M => '10 Minutes',
	    self::PASTE_EXPIRE_1H => '1 Hour',
	    self::PASTE_EXPIRE_1D => '1 Day',
	    self::PASTE_EXPIRE_1W => '1 Week',
	    self::PASTE_EXPIRE_2W => '2 Weeks',
	    self::PASTE_EXPIRE_1M => '1 Month',
    );

    /**
     * Default options to be used when making cURL requests.
     * 
     * @var array
     */
    public static $curlOptions = array(
    	CURLOPT_CONNECTTIMEOUT => 10,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_TIMEOUT => 60,
	    CURLOPT_VERBOSE => 1, 
	    CURLOPT_NOBODY => 0,
	    CURLOPT_ENCODING => 'UTF-8',
    );


    /**
     * Instantiate a new PasteBin object.
     * 
     * @param string $_apiKey
     */
    public function __construct($_apiKey = false)
    {
    	if ($_apiKey) {
    		$this->setApiKey($_apiKey);
    	}
    }

    /**
     * Set the API key used to make requests.
     * 
     * @param string $_apiKey
     */
    public function setApiKey($_apiKey)
    {
    	$this->apiDevKey = $_apiKey;
    }

    /**
     * Get the API key.
     * 
     * @return string
     */
    public function getApiKey()
    {
    	return $this->apiDevKey;
    }

    /**
     * Return last call error, whether it was a cURL error or API error.
     * If no error exists, false is returned.
     * 
     * @return string|boolean
     */
    public function getError()
    {
    	if ($this->curlError) {
    		return $this->curlError;
    	} elseif ($this->apiError) {
    		return $this->apiError;
    	}

    	return false;
    }

    /**
     * Create a new Paste. If everything is OK the return value will be the 
     * URL of the newly created Paste. If there was an error when the request
     * was sent, the return value will be false. Check the error message if
     * this is the case.
     * 
     * @param  string $_code
     * @param  string $_visibility
     * @param  string $_name
     * @param  string $_expire
     * @param  string $_format
     * @return string|boolean The URL for the newly created Paste
     */
    public function createPaste($_code, $_visibility = false, $_name = false, 
    							$_expire = false, $_format = false)
    {
    	$params = array(
    		self::API_FIELD_OPTION => self::API_OPTION_CREATE,
    		self::API_FIELD_DEV_KEY => $this->getApiKey(),
    		self::API_FIELD_PASTE_CODE => $_code,
    		self::API_FIELD_PASTE_PRIVATE => $this->_preparePrivateValue($_visibility),
    		self::API_FIELD_PASTE_NAME => (string)$_name,
    		self::API_FIELD_PASTE_EXPIRE_DATE => $this->_prepareExpireValue($_expire),
    		self::API_FIELD_PASTE_FORMAT => $this->_prepareFormatValue($_format),    		
    	);

    	return $this->_makePostRequest(self::API_POST_URL, $params);
    }

    /**
     * Make a cURL request using the POST method.
     * 
     * @param  string $_url
     * @param  array $_params
     * @return string|boolean
     */
    protected function _makePostRequest($_url, $_params)
    {
    	return $this->_makeRequest($_url, $_params, 'post');
    }

    /**
     * Make a cURL request by specifying the url, params and the method. 
     *  
     * @param  string $_url
     * @param  array $_params
     * @param  string $_type
     * @return string|boolean
     */
    protected function _makeRequest($_url, $_params, $_type = 'get') 
    {
    	$ch = $this->_prepareRequest($_url, $_type, $_params);
    	
    	$result = curl_exec($ch);
    	if (!$result) {
    		$this->curlError = curl_error($ch);
    		return false;
    	}

    	curl_close($ch);

    	return $this->_parseResponse($result);
	}

	/**
	 * Prepare the cURL request based on the http method type.
	 * 
	 * @param  string $_type
	 * @return cURL handle
	 */
	protected function _prepareRequest($_url, $_type, $_params = array())
	{
		$ch = $this->_initRequest();
		if ($ch) {
			curl_setopt($ch, CURLOPT_URL, $_url);

			switch (strtolower($_type)) {
				case 'post':
					curl_setopt($ch, CURLOPT_POST, 1);

					$postfields = $this->_encodeParams($_params);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
					break;

				case 'get':
				default:
					// this is set as default, but just to be sure
					curl_setopt($ch, CURLOPT_HTTPGET, 1);
			}
		}

		return $ch;
	}

	/**
	 * Prepare request params to be sent through cURL.
	 * 
	 * @param  array $_params
	 * @return string
	 */
	protected function _encodeParams(array $_params)
	{
		$data = array();
		foreach ($_params as $label => $value) {
			$data[] = $label .'=' . urlencode($value);
		}

		return implode('&', $data);
	}

	/**
	 * Test if the privacy value is among valid ones, 
	 * else return the default value.
	 * 
	 * @param  string $_value
	 * @return string
	 */
	protected function _preparePrivateValue($_value)
	{
		if (in_array($_value, array_keys(self::$pastePrivacyTypes))) {
			return $_value;
		}

		// default unlisted
		return self::PASTE_PRIVACY_UNLISTED; 
	}

	/**
	 * Test if the expire date value is among the valid ones,
	 * else return the default value.
	 * 
	 * @param  string $_value
	 * @return string
	 */
	protected function _prepareExpireValue($_value)
	{
		if (in_array($_value, array_keys(self::$pasteExpireTypes))) {
			return $_value;
		}

		// default 10 minutes
		return self::PASTE_EXPIRE_10M; 
	}	

	/**
	 * Set proper format of the code.
	 * 
	 * @param  string $_value
	 * @return string
	 */
	protected function _prepareFormatValue($_value)
	{
		return $_value ? $_value : 'text';
	}

	/**
	 * Initiates the cURL request used by other methods to send cURL requests.
	 * 
	 * @return cURL handle|false
	 */
	protected function _initRequest()
	{
		$ch = curl_init();
		curl_setopt_array($ch, self::$curlOptions);

		return $ch;
	}

	/**
	 * Interpret the response received from the API. 
	 * Check if there is an error returned or not. 
	 * 
	 * @param  string $_response
	 * @return boolean|string
	 */
	protected function _parseResponse($_response)
	{
		if (preg_match('/Bad API request, (.*)/', $_response, $match)) {
			$this->apiError = ucfirst($match[1]);
			return false;
		}

		return $_response;
	}
}
