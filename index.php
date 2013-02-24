<?php

define('LIBRARY', __DIR__ . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR);

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

define('APP_NAME', 'Wikitten');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'wiki.php';

Wiki::instance()->dispatch();

