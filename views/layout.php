<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo APP_NAME ?></title>
        <base href="<?php echo BASE_URL; ?>/" />

        <link rel="stylesheet" href="static/css/main.css" />

        <script type="text/javascript" src="static/js/jquery.min.js"></script>
        <script type="text/javascript" src="static/js/prettify.js"></script>

        <script type="text/javascript" src="static/js/codemirror.min.js"></script>

        <link rel="shortcut icon" href="static/img/favicon.ico" />
    </head>
<body>
    <div id="main">
        <a href="http://wikitten.vizuina.com" id="logo" target="_blank">
            <img src="static/img/logo.png" alt="" />
            <div class="bubble">Remember to check for updates!</div>
        </a>
        <div class="inner">
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span3">
                        <div id="sidebar">
                            <div class="inner">
                                <h2><span><?php echo APP_NAME ?></span></h2>
                                <?php include('tree.php') ?>
                            </div>
                        </div>
                    </div>
                    <div class="span9">
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('#logo').delay(2000).animate({
                left: '20px'
            }, 600);
        });
    </script>
</body>
</html>