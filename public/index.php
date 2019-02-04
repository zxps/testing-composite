<?php

$parts = explode('?', $_SERVER['REQUEST_URI']);
$directories = explode('/', $parts[0]);
$directories = array_filter($directories);

$filename = __DIR__ . '/' . implode('/', $directories) . '.php';
if (!file_exists($filename)) {
    die('File ' . $filename . ' not found');
}


require $filename;
