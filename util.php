<?php

$static_path = [
    "cdn.bootcss.com/jquery/1.11.2/jquery.min.js" => "static/js/jquery.min.js",
    "cdn.bootcss.com/codemirror/5.48.4/codemirror.min.js"=>"static/js/codemirror.min.js",
    "cdn.bootcss.com/codemirror/5.48.4/mode/markdown/markdown.min.js"=>"static/js/markdown.min.js",
    "cdn.bootcss.com/codemirror/5.48.4/codemirror.min.css"=>"static/css/codemirror.css",
    "cdn.bootcss.com/font-awesome/5.11.2/css/all.min.css"=>"static/css/all.min.css",
    "cdn.bootcss.com/codemirror/5.48.4/theme/tomorrow-night-bright.css"=>"static/css/darkly/codemirror-tomorrow-night-bright.css",
    "cdn.bootcss.com/twitter-bootstrap/4.3.1/css/bootstrap.min.css"=>"static/css/bootstrap.min.css",
    "cdn.jsdelivr.net/npm/bootswatch@4.3.1/dist/darkly/bootstrap.min.css"=>"static/css/darkly/bootstrap.min.css",
];

/**
 * 资源路径
 * @return string url
 */
function staticPath(string $path): string {
    global $static_path;
    if (USE_LOCAL_RESOURCE) {
        if (array_key_exists($path, $static_path)) {
            return $static_path[$path];
        }
    }
    return "//" . $path;
}

function needLogin()
{
    //login page
    if (isset($_GET['action']) && ($_GET['action'] === 'login' || $_GET['action'] === 'logout')) {
        return true;
    }
    //login check
    if (isset($_POST['username']) && isset($_POST['password'])) {
        return true;
    }
    //判断查看级别
    if (ALLOW_VIEW_LEVEL >= 2) {
        return true;
    }
    return false;
}

/**
 * 是否允许修改
 *
 * @param array $parts
 * @return bool
 */
function ifCanEdit(array $parts = []): bool
{
    if (!ifCanShow($parts)) {
        //如果不能展示，肯定不能编辑
        return false;
    }
    if (ALLOW_EDIT_LEVEL === 0) {
        return true;
    }
    if (ALLOW_EDIT_LEVEL === 3) {
        return false;
    }
    if (ALLOW_EDIT_LEVEL == 1) {
        if (Login::isLogged()) {
            return true;
        }
        if (isHideFile($parts)) {
            return false;
        }
        return true;
    }
    if (ALLOW_EDIT_LEVEL == 2) {
        if (Login::isLogged()) {
            return true;
        }
        return false;
    }
    return false;
}

/**
 * 是否允许展示
 *
 * @param array $parts 文件路径
 * @return bool
 */
function ifCanShow(array $parts = []): bool
{
    if (ALLOW_VIEW_LEVEL === 0) {
        return true;
    }
    if (ALLOW_VIEW_LEVEL === 3) {
        return false;
    }
    if (ALLOW_VIEW_LEVEL == 1) {
        if (Login::isLogged()) {
            return true;
        }
        if (isHideFile($parts)) {
            return false;
        }
        return true;
    }
    if (ALLOW_VIEW_LEVEL == 2) {
        if (Login::isLogged()) {
            return true;
        }
        return false;
    }
    return false;
}

/**
 * 是否为隐藏文件
 * @param array $parts
 * @return bool
 */
function isHideFile(array $parts = []): bool
{
    if (count($parts) == 0) {
        return false;
    }
    foreach ($parts as $part) {
        if (str_starts_with($part, '_')) {
            return true;
        }
    }
    return false;
}

function isDarkTheme(): bool
{
    if (isset($_COOKIE['ISDARK'])) {
        return '1' === $_COOKIE['ISDARK'];
    }
    return USE_DARK_THEME;
}

