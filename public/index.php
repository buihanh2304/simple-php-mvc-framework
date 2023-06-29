<?php
define('_MVC_START', microtime(true));

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

require('../system/bootstrap.php');

/** @var Kernel */
$kernel = Container::get('Kernel');

$kernel->run(Container::get('Request'));
