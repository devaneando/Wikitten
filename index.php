<?php

// Conditionally load configuration from a config.php file in
// the site root, if it exists.
if(is_file($config_file = __DIR__ . DIRECTORY_SEPARATOR . 'config.php')) {
    require_once $config_file;
}

if(!defined('APP_NAME')) {
    define('APP_NAME', 'Wikitten');
}

if(!defined('LIBRARY')) {
    define('LIBRARY', __DIR__ . DIRECTORY_SEPARATOR . 'library');
}

if(!defined('USE_PAGE_METADATA')) {
    define('USE_PAGE_METADATA', true);
}

if(!defined('ENABLE_EDITING')) {
    define('ENABLE_EDITING', false);
}

define('PLUGINS', __DIR__ . DIRECTORY_SEPARATOR . 'plugins');

$request_uri = parse_url($_SERVER['REQUEST_URI']);
$request_uri = explode("/", $request_uri['path']);
$script_name = explode("/", dirname($_SERVER['SCRIPT_NAME']));

$app_dir = array();
foreach ($request_uri as $key => $value) {
    if (isset($script_name[$key]) && $script_name[$key] == $value) {
        $app_dir[] = $script_name[$key];
    }
}

define('APP_DIR', rtrim(implode('/', $app_dir), "/"));

$https = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
    $https = true;
}

define('BASE_URL', "http" . ($https ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . APP_DIR);

unset($request_uri, $script_name, $app_dir, $https);


require_once __DIR__ . DIRECTORY_SEPARATOR . 'wiki.php';

Wiki::instance()->dispatch();

