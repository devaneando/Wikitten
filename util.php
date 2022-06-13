<?php
function needLogin(){
    //login page
    if (isset($_GET['action']) && ($_GET['action'] === 'login' || $_GET['action'] === 'logout')) {
        return true;
    }
    //login check
    if (isset($_POST['username']) && isset($_POST['password'])) {
        return true;
    }
    //private wiki
    if (defined('ACCESS_USER') && defined('ACCESS_PASSWORD') && ALLOW_EVERYONE_VIEW === false) {
        return true;
    }
    //public wiki
    if (!defined('ACCESS_USER') || !defined('ACCESS_PASSWORD')) {
        return false;
    }
    //protected
    if (defined('ACCESS_USER') && defined('ACCESS_PASSWORD') && ALLOW_EVERYONE_VIEW === true) {
        return false;
    }
    return true;
}

function ifCanManage() {
    //login user can manage
    if (Login::isLogged()) {
        return true;
    }
    //public wiki
    if ((!defined('ACCESS_USER') || !defined('ACCESS_PASSWORD')) && ENABLE_EDITING) {
        return true;
    }
    return false;
}

function isDarkTheme(){
    if (isset($_COOKIE['ISDARK'])) {
        return ('1' === $_COOKIE['ISDARK'])?true:false;
    }
    return USE_DARK_THEME;
}

function ifCanShow($fileName) {
    if(substr($fileName, 0, 1) != "_"){
        //非隐藏文件
        return true;
    }
    //隐藏文件
    if (ifCanManage()){
        return true;
    }
    return false;
}