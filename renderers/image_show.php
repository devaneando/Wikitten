<?php

function image_show($path): string{
    $path = trim($path, '/');
    return "<img src='/{$path}?raw'>";
}