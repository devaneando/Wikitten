<div class="breadcrumbs">
    <?php if ($html): ?>
        <div class="pull-right">
            <a href="#" class="btn btn-mini btn-inverse" id="toggle">Toggle source</a>
        </div>
    <?php endif ?>

    <?php $path = array(); ?>
    <ul class="unstyled">
        <li>
            <a href="<?php echo BASE_URL; ?>"><i class="icon-home icon-white"></i> /wiki</a>
        </li>
        <?php $i = 0; ?>
        <?php foreach ($parts as $part): ?>
            <?php $path[] = $part; ?>
            <li>
                &nbsp;&nbsp;&nbsp;
                <?php if (++$i == count($parts)): ?>
                    <i class="icon-file icon-white"></i>&nbsp;
                <?php else: ?>
                    <i class="icon-folder-open icon-white"></i>&nbsp;
                <?php endif ?>
                <?php echo $part; ?>
            </li>
        <?php endforeach ?>
    </ul>
    <div class="clear"></div>
</div>

<?php if ($html): ?>
    <div id="render">
        <?php echo $html; ?>
    </div>
    <script type="text/javascript">
        $('#render pre').addClass('prettyprint linenums');
        prettyPrint();

        $('#render a[href^="#"]').click(function(event) {
            event.preventDefault();
            document.location.hash = $(this).attr('href').replace('#', '');
        });
    </script>
<?php endif ?>

<div id="source">
    <textarea id="editor" class="input-block-level" rows="<?php echo substr_count($source, "\n") + 1; ?>"><?php echo $source; ?></textarea>
</div>
<script type="text/javascript">
    <?php if ($html) { ?>
        CodeMirror.defineInitHook(function () {
            $('#source').hide();
        });
    <?php } ?>

    var mode = false;
    var modes = {
        'md': 'markdown',
        'js': 'javascript',
        'php': 'php',
        'sql': 'text/x-sql',
        'py': 'python',
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

    var editor = $('#editor');
    editor.prop('data-editor', CodeMirror.fromTextArea(editor[0], {
        mode: mode,
        theme: 'default',
        lineNumbers: true,
        lineWrapping: true,
        readOnly: true
    }));

    $('#toggle').on('click', function (event) {
        event.preventDefault();

        var source = $('#source');
        var render = $('#render');

        source.toggle();
        render.toggle();

        if (source.is(':visible')) {
            $('#editor').prop('data-editor').refresh();
        }
    });
</script>
