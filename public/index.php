<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

use System\Classes\Container;
use System\Classes\Kernel;
use System\Classes\Request;

define('_MVC_START', microtime(true));

require('../system/bootstrap.php');

$container = Container::getInstance();

/** @var Kernel */
$kernel = $container->make(Kernel::class);
$request = $container->make(Request::class);

$kernel->run($request);
