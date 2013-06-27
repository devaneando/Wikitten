<?php

class Wiki
{
    protected $_renderers = array(
        'md' => 'Markdown',
        'htm' => 'HTML', 'html' => 'HTML'
    );
    protected $_ignore = "/^\..*|^CVS$/"; // Match dotfiles and CVS
	protected $_force_unignore = false; // always show these files (false to disable)

    protected $_action;

    protected $_default_page_data = array(
        'title'       => false, // will use APP_NAME by default
        'description' => 'Wikitten is a small, fast, PHP wiki.',
        'tags'        => array('wikitten', 'wiki'),
        'page'        => ''
    );

    protected function _getRenderer($extension)
    {
        if (!isset($this->_renderers[$extension])) {
            return false;
        }

        $renderer = $this->_renderers[$extension];

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'renderers' . DIRECTORY_SEPARATOR . "$renderer.php";

        return $renderer;
    }

    protected function _render($page)
    {
        $fullPath   = LIBRARY . DIRECTORY_SEPARATOR . $page;
        $path       = realpath($fullPath);
        $parts      = explode('/', $page);

        $not_found = function () use ($page) {
            $page = htmlspecialchars($page, ENT_QUOTES);
            throw new Exception("Page '$page' was not found");
        };

        if(!$this->_pathIsSafe($fullPath)) {
            $not_found();
        }            

        if (ENABLE_CREATING)
        {
            // if not found, we show Create button to create a new page if you want
            if (!file_exists($fullPath))
            {
                // Pass this to the render view, cleverly disguised as just
                // another page, so we can make use of the tree, breadcrumb,
                // etc.
                $_page              = htmlspecialchars($page, ENT_QUOTES);
                $page_data          = $this->_default_page_data;
                $page_data['title'] = 'Page not found: ' . $_page;
    
                return $this->_view('render', array(
                    'parts'     => $parts,
                    'page'      => $page_data,
                    'html'      =>
                          "<h3>Page '$_page' not found</h3>"
                        . "<br/>"
                        . "<form method='GET'>"
                        . "<input type='hidden' name='a' value='create'>"
                        . "<input type='submit' class='btn btn-primary' value='Create this page' />"
                        . "</form>"
                    ,
                    'is_dir'    => false
                ));            
            }        
        } else {
            if (!is_readable($fullPath)) 
                $not_found();
        }
        

        // Handle directories by showing a neat listing of its
        // contents
        if (is_dir($path)) {

            // If exists index.md in directory, we render it
            if (file_exists($path . DIRECTORY_SEPARATOR . 'index.md')) {
                return $this->_render('index.md');
            }


            // Get a printable version of the actual folder name:
            $dir_name   = htmlspecialchars(end($parts), ENT_QUOTES, 'UTF-8');

            // Get a printable version of the rest of the path,
            // so that we can display it with a different appearance:
            $rest_parts = array_slice($parts, 0, count($parts) - 1);
            $rest_parts = htmlspecialchars(join("/", $rest_parts), ENT_QUOTES, 'UTF-8');

            // Pass this to the render view, cleverly disguised as just
            // another page, so we can make use of the tree, breadcrumb,
            // etc.
            $page_data  = $this->_default_page_data;
            $page_data['title'] = 'Listing: ' . $dir_name;

            return $this->_view('render', array(
                'parts'     => $parts,
                'page'      => $page_data,
                'html'      =>
                      "<h3><span class=\"directory-path\">$rest_parts/</span> $dir_name</h3>"
                    . "<p>Use the tree menu on the left to select a file</p>"
                ,
                'is_dir'    => true
            ));
        }

        $finfo = finfo_open(FILEINFO_MIME);
        $mime_type = finfo_file($finfo, $path);

        if (substr($mime_type, 0, 4) != 'text') {
            // not an ASCII file, send it directly to the browser
            $file = fopen($path, 'rb');

            header("Content-Type: $mime_type");
            header("Content-Length: " . filesize($path));

            fpassthru($file);
            exit();
        }

        $source    = file_get_contents($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $renderer  = $this->_getRenderer($extension);
        $page_data = $this->_default_page_data;

        // Extract the JSON header, if the feature is enabled:
        if (USE_PAGE_METADATA) {
            list($source, $meta_data) = $this->_extractJsonFrontMatter($source);
            $page_data = array_merge($page_data, $meta_data);
        }

        // We need to know the source file in case editing is enabled:
        $page_data['file'] = $page;

        $html = false;
        if ($renderer) {
            $html = $renderer($source);
        }

        return $this->_view('render', array(
            'html'      => $html,
            'source'    => $source,
            'extension' => $extension,
            'parts'     => $parts,
            'page'      => $page_data,
            'is_dir'    => false
        ));
    }

    /**
     * Given a file path, verifies if the file is safe to touch,
     * given permissions, if it's within the library, etc.
     *
     * @param  string $path
     * @return bool
     */
    protected function _pathIsSafe($path)
    {
        if($path && strpos($path, LIBRARY) === 0) {
            return true;
        }

        return false;
    }

    /**
     * Given a string with a page's source, attempts to locate a
     * section of JSON Front Matter in the heading, and returns
     * the remaining source, and an array of extracted meta data.
     *
     * JSON Front Matter will only be considered when present
     * within two lines consisting of three dashes:
     *
     * ---
     * { "title": "hello world" }
     * ---
     *
     * Additionally, the opening and closing brackets may be dropped,
     * and this method will still interpret the content as a hash:
     *
     * ---
     * "title": "hello, world",
     * "tags":  ["hello", "world"]
     * ---
     *
     * @param  string $source
     * @return array  array($remaining_source, $meta_data)
     */
    protected function _extractJsonFrontMatter($source)
    {
       static $front_matter_regex = "/^---[\r\n](.*)[\r\n]---[\r\n](.*)/s";

        $source    = ltrim($source);
        $meta_data = array();

        if (preg_match($front_matter_regex, $source, $matches)) {
            $json   = trim($matches[1]);
            $source = trim($matches[2]);

            // Locate or append starting and ending brackets,
            // if necessary. I lazily only check the first
            // character for a bracket, so that it'll work
            // even if the user includes a hash in the last
            // line:
            if ($json[0] != '{') {
                $json = '{' . $json . '}';
            }

            // Decode & validate the JSON payload:
            $meta_data = json_decode($json, true, 512);

            // Check for errors:
            if ($meta_data === null) {
                $error   = json_last_error();
                $message = 'There was an error parsing the JSON Front Matter for this page';

                // todo: Better error information?
                if ($error == JSON_ERROR_SYNTAX) {
                    $message .= ': Incorrect JSON syntax (missing comma, or double-quotes?)';
                }

                throw new RuntimeException($message);
            }

        }

        return array($source, $meta_data);
    }

    protected function _view($view, $variables = array())
    {
        extract($variables);

        $content = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . "$view.php";

        if (!isset($layout)) {
            $layout  = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layout.php';
        }

        if (file_exists($content)) {
            ob_start();

            include($content);
            $content = ob_get_contents();
            ob_end_clean();

            if ($layout) {
                include $layout;
            } else {
                echo $content;
            }
        } else {
            throw new Exception("View $view not found");
        }
    }

    protected function _getTree($dir = LIBRARY)
    {
        $return = array('directories' => array(), 'files' => array());

        $items = scandir($dir);
        foreach ($items as $item) {
			if(preg_match($this->_ignore, $item)) {
				if($this->_force_unignore === false || !preg_match($this->_force_unignore, $item)) {
					continue;
				}
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $return['directories'][$item] = $this->_getTree($path);
                continue;
            }

            $return['files'][$item] = $item;
        }

        uksort($return['directories'], "strnatcasecmp");
        uksort($return['files'], "strnatcasecmp");

        return $return['directories'] + $return['files'];
    }

    public function dispatch()
    {
        $action = $this->_getAction();
        $actionMethod = "{$action}Action";

        if($action === null || !method_exists($this, $actionMethod)) {
            $this->_404();
        }

        $this->$actionMethod();
    }

    protected function _getAction()
    {
        if (isset($_REQUEST['a'])) {
            $action = $_REQUEST['a'];

            if (in_array("{$action}Action", get_class_methods(get_class($this)))) {
                $this->_action = $action;
            }
        } else {
            $this->_action = 'index';
        }
        return $this->_action;
    }

    protected function _json($data = array())
    {
        header("Content-type: text/x-json");
        echo (is_string($data) ? $data : json_encode($data));
        exit();
    }

    protected function _isXMLHttpRequest()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return true;
        }

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if ($headers['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                return true;
            }
        }

        return false;
    }

    protected function _404($message = 'Page not found.')
    {
        header('HTTP/1.0 404 Not Found', true);
        $page_data = $this->_default_page_data;
        $page_data['title'] = 'Not Found';

        $this->_view('uhoh', array(
            'error' => $message,
            'parts' => array('Uh-oh'),
            'page'  => $page_data
        ));

        exit;
    }

    public function indexAction()
    {
        $request = parse_url($_SERVER['REQUEST_URI']);
        $page    = str_replace("###" . APP_DIR . "/", "", "###" . urldecode($request['path']));

        if (!$page) {
            if (file_exists(LIBRARY . DIRECTORY_SEPARATOR . 'index.md')) {
                return $this->_render('index.md');
            }

            return $this->_view('index');
        }

        try {
            return $this->_render($page);

        } catch (Exception $e) {
            $page_data = $this->_default_page_data;
            $page_data['title'] = "Uh oh...";

            $this->_view('uhoh', array(
                'error' => $e->getMessage(),
                'parts' => array('Uh-oh'),
                'page'  => $page_data
            ));
            exit();
        }
    }

    /**
     * /?a=edit
     * If ENABLE_EDITING is true, handles file editing through
     * the web interface.
     */
    public function editAction()
    {
        // Bail out early if editing isn't even enabled, or
        // we don't get the right request method && params
        // NOTE: $_POST['source'] may be empty if the user just deletes
        // everything, but it should always be set.
        if (!ENABLE_EDITING || $_SERVER['REQUEST_METHOD'] != 'POST'
            || empty($_POST['ref']) || !isset($_POST['source'])) {
            $this->_404();
        }

        $ref    = $_POST['ref'];
        $source = $_POST['source'];
        $file   = base64_decode($ref);
        $path   = realpath(LIBRARY . DIRECTORY_SEPARATOR . $file);

        // Check if the file is safe to work with, otherwise just
        // give back a generic 404 aswell, so we don't allow blind
        // scanning of files:
        // @todo: we CAN give back a more informative error message
        // for files that aren't writable...
        if (!$this->_pathIsSafe($path) && !is_writable($path)) {
            $this->_404();
        }

        // Save the changes, and redirect back to the same page:
        file_put_contents($path, $source);

        $redirect_url = BASE_URL . "/$file";
        header("HTTP/1.0 302 Found", true);
        header("Location: $redirect_url");

        exit();
    }
    
    public function createAction()
    {
        $request    = parse_url($_SERVER['REQUEST_URI']);
        $page       = str_replace("###" . APP_DIR . "/", "", "###" . urldecode($request['path']));
        
        $filepath   = LIBRARY . urldecode($request['path']);
        $content    = "# " . htmlspecialchars($page, ENT_QUOTES, 'UTF-8');

        // if feature not enabled, go to 404
        if (!ENABLE_CREATING || file_exists($filepath)) $this->_404();


        // Create subdirectory recursively, if neccessary
        mkdir(dirname($filepath), 0755, true);
        
        // Save default content, and redirect back to the new page
        file_put_contents($filepath, $content);

        if (file_exists($filepath))
        {
            // Redirect to new page
            $redirect_url = BASE_URL . "/$page";
            header("HTTP/1.0 302 Found", true);
            header("Location: $redirect_url");
    
            exit();
        } 
        else
            $this->_404();
    }

    /**
     * Singleton
     * @return Wiki
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }

}
