<?php

if (!defined('APP_STARTED') && 0 !== strpos(php_sapi_name(), 'cli')) {
    die('Forbidden!');
}

if (file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    return false; // serve the requested resource as-is.
} else {
    require_once 'index.php';
}
