<?php
date_default_timezone_set("Asia/Shanghai");
if (!defined('APP_STARTED')) {
    die('Forbidden!');
}

// Example configuration file for Wikitten. To use it,
// first rename it to `config.php`.

// Custom name for your wiki:
define('APP_NAME', '魔力之所wiki');

// You can install in a dir
// define('APP_ROOT', '');

// Set the filename of the automatic homepage here
define('DEFAULT_FILE', '魔力之所.md');

// Custom path to your wiki's library:
define('LIBRARY', __DIR__ . DIRECTORY_SEPARATOR . 'library');

// Enable editing files through the interface?
// NOTE: There's currently no authentication built into Wikitten, controlling
// who does what is your responsibility.
define('ENABLE_EDITING', true);

// Enable JSON page data?
// define('USE_PAGE_METADATA', true);

// Enable the dark theme here
define('USE_DARK_THEME', true);

// Disable the Wikitten logo here
define('USE_WIKITTEN_LOGO', false);

// Enable PasteBin plugin ?
// define('ENABLE_PASTEBIN', true);
// define('PASTEBIN_API_KEY', '');

// Enable password authentication (leave this field commented to disable password)
define('ACCESS_USER', 'user');
define('ACCESS_PASSWORD', 'pass');

//view page without login
define('ALLOW_EVERYONE_VIEW', true);

// Use this as default target for external urls
define('EXTERNAL_LINK_TARGET', '_blank');

// Enable the internal wiki URL and URL fix
define('INTERNAL_WIKI_LINK', true);

// Enable a custom stylesheet
// define('CUSTOM_MARKDOWN_STYLESHEET', 'mystyle.css');
