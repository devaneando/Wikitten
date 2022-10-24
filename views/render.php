<?php
if (!defined('APP_STARTED')) {
    die('Forbidden!');
}
?>
<div class="breadcrumbs">
    <div class="pull-right">
        <?php if ($html && isset($source)): ?>
            <a href="javascript:;" class="btn-black" id="toggle">源码</a>
        <?php endif ?>
    </div>

    <?php $path = array(); ?>

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo BASE_URL; ?>">
              <i class="fas fa-home"></i> wiki
            </a>
        </li>
        <?php $i = 0; ?>
        <?php foreach ($parts as $part): ?>
            <?php $path[] = $part; ?>
            <?php $url = BASE_URL . "/" . join("/", $path); ?>
            <?php $i++; ?>
            <li class="breadcrumb-item <?php echo ($i == count($parts) ? 'active' : '')?>">
                <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($i == count($parts) && !$is_dir): ?>
                        <i class="far fa-file"></i>
                    <?php else: ?>
                        <i class="far fa-folder"></i>
                    <?php endif ?>
                    <?php echo $part; ?>
                </a>
            </li>
        <?php endforeach ?>
        
        <li class="breadcrumb-item">
              <i class="fas fa-clock"></i> <?=($time??'')?>
        </li>
        
      </ol>
    </nav>

</div>

<?php if ($html): ?>
    <div id="render">
        <?php echo $html; ?>
    </div>
    <script>
        $('#render pre').addClass('prettyprint linenums');
        prettyPrint();
        
        $('#render a[href^="#"]').click(function(event) {
            event.preventDefault();
            document.location.hash = $(this).attr('href').replace('#', '');
        });
    </script>
<?php endif ?>

<?php if (isset($source)): ?>
    <div id="source">
        <?php if (ifCanEdit($parts)): ?>
            <div class="alert alert-info">
                <i class="fa fa-pencil-alt"></i> <strong>修改模式</strong> 使用保存按钮提交你的修改
            </div>
        <?php endif ?>

        <form method="POST" action="<?php echo BASE_URL . "/?a=edit" ?>">
            <?php if (ifCanEdit($parts)): ?>
                    <input type="submit" class="btn btn-warning btn-sm" id="submit-edits" value="保存">
            <?php endif ?>
            <input type="hidden" name="ref" value="<?php echo base64_encode($page['file']) ?>">
            <textarea id="editor" name="source" class="form-control" rows="<?php echo substr_count($source, "\n") + 1; ?>"><?php echo $source; ?></textarea>

            <?php if (ifCanEdit($parts)): ?>
                <div class="form-actions">
                    <input type="submit" class="btn btn-warning btn-sm" id="submit-edits" value="保存">
                </div>
            <?php endif ?>
        </form>
    </div>

    <script>
        <?php if ($html): ?>
            CodeMirror.defineInitHook(function () {
                $('#source').hide();
            });
        <?php endif ?>

        var mode = false;
        var modes = {
            'md': 'markdown',
            'markdown': 'markdown',
            'mdown': 'markdown',
            'js': 'javascript',
            'php': 'php',
            'sql': 'text/x-sql',
            'py': 'python',
            'scm': 'scheme',
            'clj': 'clojure',
            'rb': 'ruby',
            'css': 'css',
            'hs': 'haskell',
            'lsh': 'haskell',
            'pl': 'perl',
            'r': 'r',
            'scss': 'sass',
            'sh': 'shell',
            'xml': 'xml',
            'html': 'htmlmixed',
            'htm': 'htmlmixed'
        };
        var extension = '<?php echo $extension ?>';
        if (typeof modes[extension] != 'undefined') {
            mode = modes[extension];
        }
        var codeConfig = {
            lineNumbers: true,
            lineWrapping: true,
            theme: '<?=USE_DARK_THEME?'tomorrow-night-bright':'default'?>',
            mode: mode,
            <?=ifCanEdit($parts)?'':'readOnly: true'?>
        };
        var editor = CodeMirror.fromTextArea(document.getElementById('editor'), codeConfig);
        
        $('#toggle').click(function (event) {
            event.preventDefault();
            $('#render').toggle();
            $('#source').toggle();
            if ($('#source').is(':visible')) {
                editor.refresh();
            }

        });
    </script>
<?php endif ?>
