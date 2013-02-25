<?php
function tree($array, $parent, $parts = array(), $step = 0) {

    if (!count($array)) {
        return '';
    }

    $t = '<ul class="unstyled">';

    foreach ($array as $key => $item) {
        if (is_array($item)) {
            $open = $step !== false && ($key == $parts[$step]);

            $t .= '<li class="directory'. ($open ? ' open' : '') .'">';
                $t .= '<a href="#" data-role="directory"><i class="icon icon-folder-'. ($open ? 'open' : 'close') .'"></i> ' . $key . '</a>';
                $t .= tree($item, "$parent/$key", $parts, $open ? $step + 1 : false);
            $t .=  '</li>';
        } else {
            $selected = ($item == $parts[$step]);
            $t .= '<li class="file'. ($selected ? ' active' : '') .'"><a href="'. $parent .'/'. $item . '">'.$item.'</a></li>';
        }
    }

    $t .= '</ul>';

    return $t;
}
?>

<?php echo tree($this->_getTree(), BASE_URL, $parts); ?>

<script>
    $('#sidebar a[data-role="directory"]').click(function (event) {
        event.preventDefault();

        var icon = $(this).children('.icon');
        var open = icon.hasClass('icon-folder-open');
        var subtree = $(this).siblings('ul')[0];

        icon.removeClass('icon-folder-open').removeClass('icon-folder-close');

        if (open) {
            if (typeof subtree != 'undefined') {
                $(subtree).slideUp({ duration: 100 });
            };
            icon.addClass('icon-folder-close');
        } else {
            if (typeof subtree != 'undefined') {
                $(subtree).slideDown({ duration: 100 });
            }
            icon.addClass('icon-folder-open');
        }
    });
</script>
