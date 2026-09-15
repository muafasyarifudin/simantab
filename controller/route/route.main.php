<?php
$file = __DIR__ . '/../../view/public/pages/' . $route . '.php';
require is_file($file) ? $file : __DIR__ . '/../../view/public/pages/404.php';
