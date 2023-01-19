<?php
require(__DIR__ . '/vendor/autoload.php');

$args = getopt('', ["action:"]);
$action = $args['action'];

$compress_images = new CompressImages($action);
