<?php
if (!defined('APP_STARTED')) {
    die('Forbidden!');
}

// Sanitize html content:
function e($dirty)
{
    return htmlspecialchars($dirty, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <?php if ($page['title'] === false) : ?>
            <title><?php echo e(APP_NAME) ?></title>
        <?php else: ?>
            <title><?php echo e($page['title']) ?> - <?php echo e(APP_NAME) ?></title>
        <?php endif ?>

        <base href="<?php echo BASE_URL; ?>/">

        <link rel="shortcut icon" href="static/img/favicon.ico">

        <?php if(isDarkTheme()) : ?>
            <link href="<?=staticPath("cdn.jsdelivr.net/npm/bootswatch@4.3.1/dist/darkly/bootstrap.min.css")?>" rel="stylesheet" >
            <link href="static/css/darkly/main.css" rel="stylesheet">
            <link href="static/css/darkly/prettify-dark.css" rel="stylesheet">
            <link href="<?=staticPath("cdn.bootcss.com/codemirror/5.48.4/theme/tomorrow-night-bright.css")?>" rel="stylesheet">
        <?php else: ?>
            <link href="<?=staticPath("cdn.bootcss.com/twitter-bootstrap/4.3.1/css/bootstrap.min.css")?>" rel="stylesheet">
            <link rel="stylesheet" href="static/css/prettify.css">
            <link href="static/css/main.css" rel="stylesheet">
        <?php endif; ?>


        <link href="<?=staticPath("cdn.bootcss.com/codemirror/5.48.4/codemirror.min.css")?>" rel="stylesheet">
        <link href="<?=staticPath("cdn.bootcss.com/font-awesome/5.11.2/css/all.min.css")?>" rel="stylesheet">
        <link rel="stylesheet" href="static/css/custom.css">

        <meta name="description" content="<?php echo e($page['description']) ?>">
        <meta name="keywords" content="<?php echo e(join(',', $page['tags'])) ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php if (!empty($page['author'])) : ?>
            <meta name="author" content="<?php echo e($page['author']) ?>">
        <?php endif; ?>

        <script src="<?=staticPath("cdn.bootcss.com/jquery/1.11.2/jquery.min.js")?>"></script>
        <script src="static/js/prettify.js"></script>
        <script src="<?=staticPath("cdn.bootcss.com/codemirror/5.48.4/codemirror.min.js")?>"></script>
        <script src="<?=staticPath("cdn.bootcss.com/codemirror/5.48.4/mode/markdown/markdown.min.js")?>"></script>
        <script src="static/js/diy.js"></script>

<style>
</style>
    </head>
<body>
    <div id="main">
        <div class="inner">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <div id="sidebar">
                            <div class="inner">
                                <h2><span><?php echo e(APP_NAME) ?></span></h2>
                                <?php include('tree.php') ?>
                            </div>
                        </div>
                        <?php
                        if (!Login::isLogged() && defined('ACCESS_USER') && defined('ACCESS_PASSWORD')):
                        ?>
                        <a href="<?php echo BASE_URL; ?>/?action=login" class="btn btn-secondary btn-xs">登录</a>
                        <?php
                        endif;
                        if (Login::isLogged() && defined('ACCESS_USER') && defined('ACCESS_PASSWORD')):
                        ?>
                        <a href="<?php echo BASE_URL; ?>/?action=logout" class="btn btn-secondary btn-xs">登出</a>
                        <?php
                        endif;
                        ?>
                        
                        
                        <?php
                        if (isDarkTheme()):
                        ?>
                        <a href="javascript:SetCookie('ISDARK',0);location.reload();" class="btn btn-light btn-xs" style="color: #212529;background-color: #f8f9fa;border-color: #f8f9fa;">白背景</a>
                        <?php
                        else:
                        ?>
                        <a href="javascript:SetCookie('ISDARK',1);location.reload();" class="btn btn-dark btn-xs">黑背景</a>
                        <?php
                        endif;
                        ?>
                        
                    </div>
                    <div class="col-xs-12 col-md-9">
                        <div id="content">
                            <div class="inner">
                                <?php echo $content; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function SetCookie(name, value) {
        var exp = new Date();
        exp.setTime(exp.getTime() + 30 * 24 * 60 * 60 * 1000); //3天过期
        document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString()+";path=/";
        return true;
    }
    $('table').attr('class','table table-condensed table-bordered table-striped');
    </script>
</body>
</html>
