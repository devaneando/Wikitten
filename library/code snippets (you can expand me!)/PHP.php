<?php
/**
 * Sample PHP code taken from this actual wiki
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

protected function _view($view, $variables = array())
{
    extract($variables);

    $content = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . "$view.php";
    $layout  = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layout.php';

    if (file_exists($content)) {
        ob_start();

        include($content);
        $content = ob_get_contents();
        ob_end_clean();

        include $layout;
    } else {
        throw new Exception("View $view not found");
    }
}