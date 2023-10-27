<?php

use JetBrains\PhpStorm\NoReturn;

if (!defined('APP_STARTED')) {
    die('Forbidden!');
}

class Wiki
{
    //不同后缀对应的处理器 对应renderers中的文件
    protected array $_renderers = array(
        'go' => 'showcode',
        'php' => 'showcode',
        'sh' => 'showcode',
        'css' => 'showcode',
        'py' => 'showcode',
        'rb' => 'showcode',
        'sql' => 'showcode',
        'scm' => 'showcode',
        'xml' => 'showcode',
        'c' => 'showcode',
        'js' => 'showcode',
        'json' => 'showcode',
        'puml' => 'showcode',
        'lua' => 'showcode',
        'yml' => 'showcode',
        'toml' => 'showcode',
        'md' => 'Markdown',
        'htm' => 'HTML',
        'html' => 'HTML',
    );

    /**
     * 二进制文件后缀列表
     * @var array|string[]
     */
    protected array $_bin_file_ext = array(
        'png' => 'image_show',
        'jpg' => 'image_show',
        'jpep' => 'image_show',
        'gif' => 'image_show',
        'image' => 'image_show',

        'zip' => 'download_link',
        'rar' => 'download_link',
        'gz' => 'download_link',
        'docx' => 'download_link',
        'pdf' => 'download_link',
        'xlsx' => 'download_link',
    );
    /**
     * 用于判断是否为文件
     *
     * @var array|bool[]
     */
    protected array $_file_ext = array(
        "woff" => true,
        "woff2" => true,
    );

    protected string $_action;

    protected array $_default_page_data = array(
        'title' => false, // will use APP_NAME by default
        'description' => 'Wikitten is a small, fast, PHP wiki.',
        'tags' => array('wikitten', 'wiki'),
        'page' => ''
    );

    const DIR_KEY = 0;
    const FILE_KEY = 1;
    /**
     * Singleton
     * @return Wiki
     */
    public static function instance(): Wiki
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new Wiki();
        }
        return $instance;
    }

    public function __construct() {
        $this->_renderers = $this->_renderers + $this->_bin_file_ext;
        $this->_file_ext = array_merge($this->_file_ext, $this->_renderers);
    }

    /**
     * @param string $extension
     * @return string|callable
     */
    protected function _getRenderer(string $extension)
    {
        if (!isset($this->_renderers[$extension])) {
            return false;
        }

        $renderer = $this->_renderers[$extension];

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'renderers' . DIRECTORY_SEPARATOR . "$renderer.php";

        return $renderer;
    }

    protected function _render($page): void
    {
        $fullPath = LIBRARY . DIRECTORY_SEPARATOR . $page;
        $path = realpath(LIBRARY . DIRECTORY_SEPARATOR . $page);
        $parts = explode('/', trim($page,'/'));
        $extension = "";
        $isMatched = preg_match('/\.([a-z]{1,4})$/', $page, $matches);
        if ($isMatched) {
            $extension = $matches[1];
        }
        $not_found = function () use ($page) {
            $page = htmlspecialchars($page, ENT_QUOTES);
            throw new Exception("页面'$page'未找到.");
        };

        if (!$this->_pathIsSafe($fullPath)) {
            $not_found();
        }

        // Handle directories by showing a neat listing of its
        // contents
        if (is_dir($path)) {
            if (!file_exists($path)) {
                $not_found();
            }

            if (file_exists($path . DIRECTORY_SEPARATOR . 'index.md')) {
                $this->_render(implode('/', $parts) . DIRECTORY_SEPARATOR . 'index.md');
                return;
            }

            // Get a printable version of the actual folder name:
            $dir_name = htmlspecialchars(end($parts), ENT_QUOTES, 'UTF-8');

            // Get a printable version of the rest of the path,
            // so that we can display it with a different appearance:
            $rest_parts = array_slice($parts, 0, count($parts) - 1);
            $rest_parts = htmlspecialchars(join("/", $rest_parts), ENT_QUOTES, 'UTF-8');

            // Pass this to the render view, cleverly disguised as just
            // another page, so we can make use of the tree, breadcrumb,
            // etc.
            $page_data = $this->_default_page_data;
            $page_data['title'] = '目录: ' . $dir_name;

            $files = scandir($path);
            $filesCount = count($files);
            uasort($files, "strnatcasecmp");
            $list = "";
            if (2 < count($files)) {
                //排除隐藏目录
                if (!ifCanShow([$dir_name])){
                    //隐藏目录报错
                    $not_found();
                }
                $list = "<h2>目录:{$page}</h2><h5>文件总数:{$filesCount}</h5><ul>\n";
                foreach ($files as $file) {
                    //忽略.开头文件
                    if (preg_match('/^\..*$/', $file)) {
                        continue;
                    }
                    $list .= "<li><a href=\"". $_SERVER['REQUEST_URI'] ."/${file}\">${file}</a></li>\n";
                }
                $list .= "</ul>\n";
            }else{
                rmdir($path);
            }

            $this->_view('render', array(
                'parts' => $parts,
                'page' => $page_data,
                'html' => $list,
                'is_dir' => true
            ));
            return;
        }
        //后缀
        if (empty($path)) {
            //$path为空代表文件没找到
            if (!ifCanEdit($parts)) {
                $not_found();
            }
            if (false === $this->_getRenderer($extension)) {
                $not_found();
            } elseif (!file_exists($fullPath)) {

                // Pass this to the render view, cleverly disguised as just
                // another page, so we can make use of the tree, breadcrumb,
                // etc.
                $_page              = htmlspecialchars($page, ENT_QUOTES);
                $page_data          = $this->_default_page_data;
                $page_data['title'] = 'Page not found: ' . $_page;

                $this->_view('render', array(
                    'parts'     => $parts,
                    'page'      => $page_data,
                    'html'      =>
                        "<h3>Page '$_page' not found.</h3>"
                        . "<br/>"
                        . (ifCanEdit($parts) ? "<form method='GET'>"
                            . "<input type='hidden' name='a' value='create'>"
                            . "<input type='submit' class='btn btn-primary' value='新建文件' />"
                            . "</form>" : ''),
                    'is_dir'    => false
                ));
                return;
            }
        }

        $finfo = finfo_open(FILEINFO_MIME);
        $mime_type = trim(finfo_file($finfo, $path));
        if (str_contains($mime_type, 'image')) {
            //图片文件的后缀名都设置为image
            $extension = 'image';
        }

        $time = filemtime($path);
        $renderer = $this->_getRenderer($extension);
        //(!str_starts_with($mime_type, 'application/json') &&
        //    !str_starts_with($mime_type, 'text')
        //    && !str_starts_with($mime_type, 'inode/x-empty'))
        if (($renderer === false && !str_starts_with($mime_type, 'text')) || array_key_exists("raw", $_GET)) {
            // not an ASCII file, send it directly to the browser
            $file = fopen($path, 'rb');
            switch ($extension){
                case "json": $mime_type = "application/json"; break;
                case "js": $mime_type = "application/javascript"; break;
                case "html": $mime_type = "text/html"; break;
                case "txt":
                case "md": $mime_type = "text/plain"; break;
                case "jpg":
                case "png":
                case "gif":
                case "webp": $mime_type = "image/*"; break;
                default:
                    $mime_type = "";
            }
            header("Content-Type: {$mime_type}; charset=utf-8");
            header("Content-Length: " . filesize($path));

            fpassthru($file);
            exit();
        }
        if(!ifCanShow($parts)){
            $not_found();
        }
        $page_data = $this->_default_page_data;

        $page_data['file'] = $page;

        $html = false;
        $source = false;

        if ($renderer === 'image_show') {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'renderers' . DIRECTORY_SEPARATOR . "image_show.php";
            $html = image_show($page);
        }
        if ($renderer === 'download_link') {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'renderers' . DIRECTORY_SEPARATOR . "download_link.php";
            $html = download_link($page);
        }

        if ($renderer == 'HTML') {
            $html = HTML(file_get_contents($path));
            $source = file_get_contents($path);
        }
        if ($renderer == 'Markdown') {
            // 换markdown引擎
            $html = \tp_Markdown\Markdown::convert(file_get_contents($path));
            $source = file_get_contents($path);
            // $html = \Wikitten\MarkdownExtra::defaultTransform($source);
        }
        //默认的代码展示方法
        if (false === $html){
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'renderers' . DIRECTORY_SEPARATOR . "showcode.php";
            $html = showcode(file_get_contents($path), $extension);
            $source = file_get_contents($path);
        }

        if (empty(trim($html))) {
            $html = "<h1>此页面木有东西</h1>\n";
            $source = $parts[0];
        }
        $page_data['title'] = $parts[count($parts) - 1];
        $this->_view('render', array(
            'time' => date('Y-m-d H:i:s',$time),
            'html' => $html,
            'source' => $source,
            'extension' => $extension,
            'parts' => $parts,
            'page' => $page_data,
            'is_dir' => false,
        ));
    }

    /**
     * Given a file path, verifies if the file is safe to touch,
     * given permissions, if it's within the library, etc.
     *
     * @param string $path
     * @return bool
     */
    protected function _pathIsSafe(string $path): bool
    {
        if ($path && str_starts_with($path, LIBRARY)) {
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
     * @param string $source
     * @return array  array($remaining_source, $meta_data)
     */
    protected function _extractJsonFrontMatter(string $source): array
    {
        static $front_matter_regex = "/^---[\r\n](.*)[\r\n]---[\r\n](.*)/s";

        $source = ltrim($source);
        $meta_data = array();

        if (preg_match($front_matter_regex, $source, $matches)) {
            $json = trim($matches[1]);
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
                $error = json_last_error();
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

    /**
     * render内容
     * @param $view
     * @param $variables
     * @return string
     */
    protected function _buildHTML($view, $variables = array()): string
    {
        extract($variables);
        $content = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . "{$view}.php";
        if (file_exists($content)) {
            ob_start();
            include($content);
            $htmlContent = ob_get_contents();
            ob_end_clean();
            return $htmlContent;
        }
        return "null";
    }

    protected function _view($view, $variables = array()): void
    {
        extract($variables);
        $treeData = $this->_getTree($variables['parts']??[]);
        $treeHTML = self::buildTreeUl($treeData, BASE_URL, $parts ?? array());
        $layout = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layout.php';
        $content = $this->_buildHTML($view, $variables);
        include $layout;
        exit;
    }

    /**
     * 获取目录递归结构
     *
     * @param array $tPath 目标路径
     * @param array $dir 当前遍历路径
     * @param int $deep 当前目录深度
     * @return array
     */
    protected function _getTree(array $tPath, array $dir = [], int $deep = 0): array
    {
        $return = array(self::DIR_KEY => array(), self::FILE_KEY => array());
        // 当前目录
        $targetPath = LIBRARY . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $dir);
        $items = scandir($targetPath);

        foreach ($items as $item) {
            if (str_starts_with($item, '.') || str_starts_with($item, 'CVS')) {
                continue;
            }
            //不展示隐藏文件
            if (!ifCanShow([$item])) {
                continue;
            }

            //优先通过后缀判断是文件还是目录
            if (array_key_exists(pathinfo($item, PATHINFO_EXTENSION), $this->_file_ext)) {
                $return[self::FILE_KEY][$item] = $item;
                continue;
            }
            //判断是否为目录
            if (is_dir(LIBRARY . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_merge($dir, [$item])))) {
                //命中当前目录子目录，展开
                if (array_key_exists($deep, $tPath) && $tPath[$deep] == $item) {
                    $return[self::DIR_KEY][$item] = $this->_getTree($tPath, array_merge($dir, [$item]), $deep+1);
                    continue;
                }
                $return[self::DIR_KEY][$item] = [];
                continue;
            }
            $return[self::FILE_KEY][$item] = $item;
        }
        uksort($return[self::DIR_KEY], "strnatcasecmp");
        uksort($return[self::FILE_KEY], "strnatcasecmp");

        return $return;
    }

    protected function buildTreeUl($array, $parent, $parts = array(), $step = 0): string
    {
        if (count($array) == 0) {
            return '<ul></ul>';
        }

        $tid = ($step === 0) ? 'id="tree"' : '';
        $t = '<ul class="unstyled" '.$tid.'>';
        if ($step === false){
            return $t . "</ul>\n\n";
        }
        $t .= $this->buildTreeLiList($array, $parent, $parts, $step);
        $t .= "</ul>\n\n";

        return $t;
    }
    protected function buildTreeLiList($array, $parent, $parts = array(), $step = 0): string
    {
        $t = "";
        foreach ($array[0] as $key => $item) {
            $open = $step !== false && (isset($parts[$step]) && $key === $parts[$step]);

            $t .= '<li class="directory'. ($open ? ' open' : '') .'">';
            $t .= '<a href="/?a=tree&dir=' . urlencode(trim("{$parent}/{$key}", "/")) . '" data-role="directory"><i class="far fa-folder'. ($open ? '-open' : '') .'"></i>' . $key . '</a>';
            $t .= $this->buildTreeUl($item, "$parent/$key", $parts, $open ? $step + 1 : false);
            $t .=  '</li>';
        }
        //文件
        foreach ($array[1] as $item) {
            $selected = (isset($parts[$step]) && $item === $parts[$step]);

            $tail = "";
            $extension = pathinfo($item, PATHINFO_EXTENSION);
            if (array_key_exists($extension, $this->_bin_file_ext)) {
                $tail = '?frame';
            }
            $t .= '<li class="file'. ($selected ? ' active' : '') .'"><a href="'. $parent .'/'. $item . $tail . '">'.$item."</a></li>\n";
        }
        return $t;
    }
    public function dispatch(): void
    {
        if (!function_exists("finfo_open")) {
            die("<p>Please enable the PHP Extension <code style='background-color: #eee; border: 1px solid #ccc; padding: 3px; border-radius: 3px; line-height: 1;'>FileInfo.dll</code> by uncommenting or adding the following line:</p><pre style='background-color: #eee; border: 1px solid #ccc; padding: 5px; border-radius: 3px;'><code><span style='color: #999;'>;</span>extension=php_fileinfo.dll <span style='color: #999; margin-left: 25px;'># You can just uncomment by removing the semicolon (;) in the front.</span></code></pre>");
        }
        $action = $this->_getAction();
        $actionMethod = "{$action}Action";

        if ($action === null || !method_exists($this, $actionMethod)) {
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

//    protected function _json($data = array())
//    {
//        header("Content-type: text/x-json");
//        echo(is_string($data) ? $data : json_encode($data));
//        exit();
//    }
//
    protected function _isXMLHttpRequest(): bool
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return true;
        }
        return false;
    }

    protected function _404($message = 'Page not found.'): void
    {
        header('HTTP/1.0 404 Not Found', true);
        $page_data = $this->_default_page_data;
        $page_data['title'] = 'Not Found';

        $this->_view('uhoh', array(
            'error' => $message,
            'parts' => array('Uh-oh'),
            'page' => $page_data
        ));
        exit;
    }

    public function indexAction(): void
    {
        $request = parse_url($_SERVER['REQUEST_URI']);
        $page = str_replace("###" . APP_DIR . "/", "", "###" . urldecode($request['path']));
        if (!$page) {
            if (file_exists(LIBRARY . DIRECTORY_SEPARATOR . DEFAULT_FILE)) {
                $this->_render(DEFAULT_FILE);
                return;
            }
            
            $this->_view('index', array(
                'page' => $this->_default_page_data
            ));
            return;
        }
        
        try {
            $this->_render($page);
        } catch (Exception $e) {
            $this->_404($e->getMessage());
        }
    }

    /**
     * 构造树形结构
     * @return void
     */
    public function treeAction(): void
    {
        $dir = $_GET['dir'];
        $tPath = explode('/', $dir);
        $treeArray = $this->_getTree($tPath, $tPath, count($tPath));
        echo self::buildTreeLiList($treeArray, BASE_URL .'/'. $dir, $tPath, count($tPath));
    }
    /**
     * /?a=edit
     * If ENABLE_EDITING is true, handles file editing through
     * the web interface.
     */
    public function editAction(): void
    {
        // Bail out early if editing isn't even enabled, or
        // we don't get the right request method && params
        // NOTE: $_POST['source'] may be empty if the user just deletes
        // everything, but it should always be set.
        if ($_SERVER['REQUEST_METHOD'] != 'POST'
            || empty($_POST['ref']) || !isset($_POST['source'])
        ) {
            $this->_404();
        }

        $ref = $_POST['ref'];
        $source = $_POST['source'];
        $file = base64_decode($ref);
        $path = realpath(LIBRARY . DIRECTORY_SEPARATOR . $file);

        if (!ifCanEdit(explode('/', trim($path,'/')))) {
            $this->_404();
        }

        // Check if the file is safe to work with, otherwise just
        // give back a generic 404 aswell, so we don't allow blind
        // scanning of files:
        // @todo: we CAN give back a more informative error message
        // for files that aren't writable...
        if (!$this->_pathIsSafe($path) && !is_writable($path)) {
            $this->_404();
        }

        // Check if empty
        if(trim($source)){
            // Save the changes, and redirect back to the same page
            file_put_contents($path, $source);
        }else{
            // Delete file and redirect too (but it will return 404)
            unlink($path);
        }

//        $redirect_url = BASE_URL . "/$file";
//        header("HTTP/1.0 302 Found", true);
//        header("Location: $redirect_url");
        echo "<script>window.history.go(-1);</script>";

        exit();
    }

    public function createAction(): void
    {
        $request    = parse_url($_SERVER['REQUEST_URI']);
        //过滤wiki当前目录
        if(defined('APP_ROOT') && '/' !== APP_ROOT){
            $requestPath = urldecode(str_replace(APP_ROOT, '', $request['path']));
        }else{
            $requestPath = urldecode($request['path']);
        }
        if (!ifCanEdit(explode($requestPath, '/'))) {
            $this->_404();
        }
        //页面标题
        $title = trim($requestPath, "\/");
        //文档路径
        $filepath   = LIBRARY . $requestPath;
        //默认内容
        $content    = "## " . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "\n\n```\ncode here\n```";
        // if feature not enabled, go to 404
        if (file_exists($filepath)) {
            $this->_404($filepath);
        }
        // Create subdirectory recursively, if neccessary
        if(!file_exists(dirname($filepath))){
            mkdir(dirname($filepath), 0755, true);
        }

        // Save default content, and redirect back to the new page
        file_put_contents($filepath, $content);
        if (file_exists($filepath)) {
            // Redirect to new page
            $redirect_url = BASE_URL . $requestPath;
            header("HTTP/1.0 302 Found", true);
            header("Location: $redirect_url");

            exit();
        } else {
            $this->_404();
        }
    }
}
