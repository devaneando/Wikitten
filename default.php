<?php

const APP_STARTED = true;

// Conditionally load configuration from a config.php file in
// the site root, if it exists.
if (is_file($config_file = __DIR__ . DIRECTORY_SEPARATOR . 'config.php')) {
    require_once $config_file;
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Wikitten');
}

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname($_SERVER['PHP_SELF']));
}

if (!defined('LIBRARY')) {
    define('LIBRARY', __DIR__ . DIRECTORY_SEPARATOR . 'library');
}

if (!defined('DEFAULT_FILE')) {
    define('DEFAULT_FILE', 'index.md');
}

if (!defined('ACCESS_USER')) {
    define('ACCESS_USER', 'user');
}

if (!defined('ACCESS_PASSWORD')) {
    define('ACCESS_PASSWORD', 'pass');
}

if (!defined('EXTERNAL_LINK_TARGET')) {
    define('EXTERNAL_LINK_TARGET', '_blank');
}

if (!defined('EXTERNAL_LINK_TARGET')) {
    define('EXTERNAL_LINK_TARGET', '_blank');
}

if (!defined('INTERNAL_WIKI_LINK')) {
    define('INTERNAL_WIKI_LINK', true);
}

if (!defined('ALLOW_VIEW_LEVEL')) {
    define('ALLOW_VIEW_LEVEL', 1);
}

if (!defined('ALLOW_EDIT_LEVEL')) {
    define('ALLOW_EDIT_LEVEL', 2);
}

if (!defined('USE_LOCAL_RESOURCE')) {
    define('USE_LOCAL_RESOURCE', true);
}

define('PLUGINS', __DIR__ . DIRECTORY_SEPARATOR . 'plugins');
