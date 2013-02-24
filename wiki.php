<?php

class Wiki
{
    protected $_renderers = array(
        'md' => 'Markdown',
        'htm' => 'HTML', 'html' => 'HTML'
    );
    protected $_ignore = array(".", "..", ".svn", ".git", ".hg", "CVS", ".sass-cache", ".bundle", ".gitignore", ".gitkeep", ".sass-cache", ".DS_Store");

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
            throw new Exception("Page $page not found");
        };

        $path = realpath(LIBRARY . $page);

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

        if ($renderer) {
            $html = $renderer($source);
        }

        $parts = explode('/', $page);

        return $this->_view('render', array(
            'html' => $html,
            'source' => $source,
            'extension' => $extension,
            'parts' => $parts
        ));
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

    /**
     * Directories first
     */
    protected function _getTreeSorter($dir = LIBRARY)
    {
        return function ($a, $b) use ($dir) {
            $a = $dir . DIRECTORY_SEPARATOR . $a;
            $b = $dir . DIRECTORY_SEPARATOR . $b;

            $is_dir_a = is_dir($a);
            $is_dir_b = is_dir($b);

            if ($is_dir_a xor $is_dir_b) {
                if ($is_dir_a) {
                    return 1;
                } else {
                    return -1;
                }
            } else {
                return strnatcmp($a, $b);
            }
        };
    }

    protected function _getTree($dir = LIBRARY)
    {
        $return = array();

        $items = scandir($dir);
        foreach ($items as $item) {
            if (in_array($item, $this->_ignore)) {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $return[$item] = $this->_getTree($path);
                continue;
            }

            $return[] = $item;
        }

        uasort($return, $this->_getTreeSorter($dir));

        return $return;
    }

    public function dispatch()
    {
        $action = $this->_getAction() . "Action";
        $this->$action();
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

    protected function _404()
    {
        header('HTTP/1.0 404 Not Found', true);
        exit('404 Not Found');
    }

    public function indexAction()
    {
        $request = parse_url($_SERVER['REQUEST_URI']);
        $page    = str_replace("###" . APP_DIR . "/", "", "###" . urldecode($request['path']));

        if (!$page) {
            if (file_exists(LIBRARY . DIRECTORY_SEPARATOR . 'index.md')) {
                return $this->_render('index.md');
            }

            return $this->_view('index', array('layout' => false));
        }

        try {
            return $this->_render($page);

        } catch (Exception $e) {
            // TODO: friendly error page
            echo $e->getMessage();
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