<?php
require_once __DIR__ . '/default.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'login.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'wiki.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'util.php';
session_start();

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

//define('BASE_URL', "http" . ($https ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . APP_DIR);
define('BASE_URL', APP_DIR);

unset($config_file, $request_uri, $script_name, $app_dir, $https);

if (needLogin()) {
    Login::instance()->dispatch();
}

Wiki::instance()->dispatch();
