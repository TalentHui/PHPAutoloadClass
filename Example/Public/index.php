<?php
/********************************************************************************
 * Copyright: PhpStorm - index.php
 * Author:    吴辉
 * Date:      2018-04-04 15:49
 * Desc:      功能描述
 *******************************************************************************/
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR);

include '../Libraries/ClassLoader.php';
$ClassLoader = new ClassLoader(false, true);
$ClassLoader::registerNamespace('Libraries', ROOT_DIR);
$ClassLoader::register();

\Libraries\Module\Model::run();

$ClassLoader::registerNamespace('.', ROOT_DIR . 'Root');

Two::run();
