<?php
if (!defined('APP_STARTED')) {
    die('Forbidden!');
}

// Sanitize html content:
function e($dirty): string
{
    return htmlspecialchars($dirty, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <?php if (empty($page['title'])): ?>
            <title><?php echo e(APP_NAME) ?></title>
        <?php else: ?>
            <title><?php echo e($page['title']) ?> - <?php echo e(APP_NAME) ?></title>
        <?php endif ?>

        <base href="<?php echo BASE_URL; ?>/">

        <link rel="shortcut icon" href="static/img/favicon.ico">

        <?php if (isDarkTheme()): ?>
            <link href="<?=staticPath("cdn.jsdelivr.net/npm/bootswatch@4.3.1/dist/darkly/bootstrap.min.css")?>" rel="stylesheet" >
            <link rel="stylesheet" href="static/css/darkly/main.css">
        <?php else: ?>
            <link href="<?=staticPath("cdn.bootcss.com/twitter-bootstrap/4.3.1/css/bootstrap.min.css")?>" rel="stylesheet">
            <link rel="stylesheet" href="static/css/main.css">
        <?php endif; ?>
		<link rel="stylesheet" href="static/css/custom.css">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php if (!empty($page['author'])): ?>
            <meta name="author" content="<?php echo e($page['author']) ?>">
        <?php endif; ?>


        <script src="<?=staticPath("cdn.bootcss.com/jquery/1.11.2/jquery.min.js")?>"></script>
        <script src="static/js/prettify.js"></script>
    </head>
<body>
    <div id="main">
        <div class="inner">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 mx-auto">
                      <div id="content">
                          <h3><span><?php echo e(APP_NAME) ?></span></h3>

                          <div class="inner">
                            <?php if (isset($error)): ?>
                              <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="" method="post">
                              <div class="input-group">
                                  <input name="username" type="text" placeholder="输入用户名" class="form-control input-sm">

                                  <a title="Clear current search..." class="input-group-addon input-sm">
                                      <i class="glyphicon glyphicon-user"></i>
                                  </a>
                              </div>

                              <br />

                              <div class="input-group">
                                  <input name="password" type="password" placeholder="输入密码" class="form-control input-sm">

                                  <a title="Clear current search..." class="input-group-addon input-sm">
                                      <i class="glyphicon glyphicon-lock"></i>
                                  </a>
                              </div>

                              <br />

                              <button type="submit" name="login" class="btn btn-sm btn-primary">Send!</button>
                            </form>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
