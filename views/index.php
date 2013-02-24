<!DOCTYPE html>
<html>
    <head>
        <title>Wikiraptor</title>
        <base href="<?php echo BASE_URL; ?>/" />

        <link rel="stylesheet" href="static/css/main.css" />
        <script type="text/javascript" src="static/js/jquery.min.js"></script>

        <link rel="shortcut icon" href="static/img/favicon.ico" />
    </head>
<body>
    <div id="main">
        <div class="inner">
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span4 offset4">
                        <div id="sidebar">
                            <div class="inner">
                                <h2><span>Wikiraptor</span></h2>
                                <?php include('tree.php') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>