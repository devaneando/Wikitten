<?php

function download_link($path) {
    $path = trim($path, '/');
    return "<a href='/{$path}?raw' target='_blank'>下载:{$path}</a>";
}