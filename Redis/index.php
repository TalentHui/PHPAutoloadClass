<?php
define('ROOT_DIR', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

include '../ClassLoader.php';
$ClassLoader = new ClassLoader($f_open_debug = false, $f_use_apc = true);
$ClassLoader::registerNamespace('RedisVendor', ROOT_DIR);
$ClassLoader::register();
