<?php

class Wiki
{
    protected $_renderers = array(
        'md' => 'Markdown',
        'htm' => 'HTML', 'html' => 'HTML'
    );
    protected $_ignore = array(".", "..", ".svn", ".git", ".hg", "CVS", ".sass-cache", ".bundle", ".gitignore", ".gitkeep", ".sass-cache", ".DS_Store");

    protected $_action;

    protected $_default_page_data = array(
        'title'       => false, // will use APP_NAME by default
        'description' => 'Wikitten is a small, fast, PHP wiki.',
        'tags'        => array('wikitten', 'wiki')
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
        $not_found = function () use ($page) {
            $page = htmlspecialchars($page, ENT_QUOTES);
            throw new Exception("Page '$page' was not found");
        };

        $path = realpath(LIBRARY . DIRECTORY_SEPARATOR . $page);

        if (!$path) {
            return $not_found();
        }

        if (strpos($path, LIBRARY) !== 0) {
            return $not_found();
        }

        if (!file_exists($path)) {
            return $not_found();
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
        if(USE_PAGE_METADATA) {
            list($source, $meta_data) = $this->_extractJsonFrontMatter($source);
            $page_data = array_merge($page_data, $meta_data);
        }

        $html = false;
        if ($renderer) {
            $html = $renderer($source);
        }

        $parts = explode('/', $page);

        return $this->_view('render', array(
            'html'      => $html,
            'source'    => $source,
            'extension' => $extension,
            'parts'     => $parts,
            'page'      => $page_data
        ));
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
        static $front_matter_regex = "/^---\n(.*)\n---\n(.*)/s";

        $source    = ltrim($source);
        $meta_data = array();

        if(preg_match($front_matter_regex, $source, $matches)) {
            $json   = trim($matches[1]);
            $source = trim($matches[2]);

            // Locate or append starting and ending brackets,
            // if necessary. I lazily only check the first
            // character for a bracket, so that it'll work
            // even if the user includes a hash in the last
            // line:
            if($json[0] != '{') {
                $json = '{' . $json . '}';
            }

            // Decode & validate the JSON payload:
            $meta_data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

            // Check for errors:
            if($meta_data === null) {
                $error   = json_last_error();
                $message = 'There was an error parsing the JSON Front Matter for this page';

                // todo: Better error information?
                if($error == JSON_ERROR_SYNTAX) {
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
            if (in_array($item, $this->_ignore)) {
                continue;
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
