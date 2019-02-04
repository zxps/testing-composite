<?php

file_put_contents(__DIR__ . '/cnt.txt', '.', FILE_APPEND | FILE_BINARY);
$length = filesize(__DIR__ .'/cnt.txt');
if ($length > 300) {
    file_put_contents(__DIR__ . '/cnt.txt', '');
    file_put_contents(__DIR__ . '/cnt.res',
        $length + (int) file_get_contents(__DIR__.'/cnt.res'));
}
?>
<html>
<head>
    <title>Script call counter</title>
</head>
<body>
<?php include __DIR__ . '/../header.php' ?>
<div class="container">
<h3>Script call counter with counter.txt</h3>
</div>
</body>
</html>