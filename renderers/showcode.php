<?php

function showcode($source, $extension) {
    require_once dirname(__FILE__) . '/Markdown.php';
    $source = "```{$extension}\n" . $source . "\n```";
    return \tp_Markdown\Markdown::convert($source);
}