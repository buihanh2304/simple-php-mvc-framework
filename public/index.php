<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/


define('_MVC_START', microtime(true));

require('../system/bootstrap.php');

/** @var Kernel */
$kernel = Container::get(Kernel::class);

$kernel->run(Container::get(Request::class));
