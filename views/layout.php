<?php
// Sanitize html content:
function e($dirty) {
    return htmlspecialchars($dirty, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <?php if($page['title'] === false): ?>
            <title><?php echo e(APP_NAME) ?></title>
        <?php else: ?>
            <title><?php echo e($page['title']) ?> - <?php echo e(APP_NAME) ?></title>
        <?php endif ?>

        <base href="<?php echo BASE_URL; ?>/">

        <link rel="shortcut icon" href="static/img/favicon.ico">

        <?php if (USE_DARK_THEME): ?>
            <link rel="stylesheet" href="static/css/bootstrap_dark.min.css">
            <link rel="stylesheet" href="static/css/dark/prettify-dark.css">
            <link rel="stylesheet" href="static/css/codemirror.css">
            <link rel="stylesheet" href="static/css/main_dark.css">
            <link rel="stylesheet" href="static/css/dark/codemirror-tomorrow-night-bright.css">
        <?php else: ?>
            <link rel="stylesheet" href="static/css/bootstrap.min.css">
            <link rel="stylesheet" href="static/css/prettify.css">
            <link rel="stylesheet" href="static/css/codemirror.css">
            <link rel="stylesheet" href="static/css/main.css">
        <?php endif; ?>
		<link rel="stylesheet" href="static/css/custom.css">

        <meta name="description" content="<?php echo e($page['description']) ?>">
        <meta name="keywords" content="<?php echo e(join(',', $page['tags'])) ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <?php if(!empty($page['author'])): ?>
            <meta name="author" content="<?php echo e($page['author']) ?>">
        <?php endif; ?>

        <script src="static/js/jquery.min.js"></script>
        <script src="static/js/prettify.js"></script>
        <script src="static/js/codemirror.min.js"></script>
    </head>
<body>
    <div id="main">
        <?php if(USE_WIKITTEN_LOGO === true): ?>
            <a href="http://wikitten.vizuina.com" id="logo" target="_blank" class="hidden-phone">
                <img src="static/img/logo.png" alt="">
                <div class="bubble">Remember to check for updates!</div>
            </a>
        <?php endif; ?>
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
        <?php if(USE_WIKITTEN_LOGO === true): ?>
            $(document).ready(function () {
                $('#logo').delay(2000).animate({
                    left: '20px'
                }, 600);
            });
        <?php endif; ?>
    </script>
</body>
</html>
