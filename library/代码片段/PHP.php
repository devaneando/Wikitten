<?php
/**
 * Sample PHP code taken from this actual wiki
 */
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

    return array_merge($return['directories'], $return['files']);
}