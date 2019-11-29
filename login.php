<?php
if (!defined('APP_STARTED')) {
    die('Forbidden!');
}

/**
 * This class is only instantiated if the ACCESS_USER and ACCESS_PASSWORD constants are defined
 *
 * @todo Add log for the login attempts and limit as soon as possible.
 */
class Login
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            $this->doLogout();

            header("location: " . BASE_URL);
            exit;
        }
    }

    /**
     * Check if the user is logged
     * @return boolean
     */
    public static function isLogged()
    {
        if (empty($_SESSION['ACCESS_USER']) || empty($_SESSION['ACCESS_PASSWORD'])) {
            return false;
        }
        if ($_SESSION['ACCESS_USER'] !== ACCESS_USER || $_SESSION['ACCESS_PASSWORD'] !== ACCESS_PASSWORD) {
            return false;
        }

        return true;
    }

    /**
     * Do the login
     * @param  string $ip       IP address
     * @param  string $username Username
     * @param  string $password Password
     * @return boolean
     */
    private function doLogin($ip, $username, $password)
    {
        // Check the access to this function, using logs and ip
        //--> to be implemented

        // Check credentials
        if ($username !== ACCESS_USER || $password !== ACCESS_PASSWORD) {
            return false;
        }

        $_SESSION['ACCESS_USER'] = $username;
        $_SESSION['ACCESS_PASSWORD'] = $password;

        return true;
    }

    /**
     * Logout from the password protected area
     * @return boolean Always true
     */
    private function doLogout()
    {
        $_SESSION['ACCESS_USER'] = '';
        $_SESSION['ACCESS_PASSWORD'] = '';

        return true;
    }

    /**
     * Get the IP address of the visitor
     * @return string
     */
    private function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Render the login page, if the authentication is not performed
     */
    public function dispatch()
    {
        // Stop if user is aready logged in (exception from negative first)
        if ($this->isLogged()) {
            return true;
        }

        $ip = $this->getRealIpAddr();

        $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : null;
        $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : null;

        if (isset($_POST['login'])) {
            if (!$username || !$password) {
                $error = 'Please complete both fields.';
            } else {
                if (!$this->doLogin($ip, $username, $password)) {
                    $error = "Wrong password.";
                } else {
                    return true;
                }
            }
        }

        // Show the login layout and stop
        $layout = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'login.php';
        include($layout);

        exit;
    }

    /**
     * Singleton
     * @return Login
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}
