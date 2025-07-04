<?php

use System\Classes\Container;
use System\Classes\Kernel;
use System\Classes\Request;

define('_MVC_START', microtime(true));

require('../system/bootstrap.php');

$container = Container::getInstance();

$kernel = $container->make(Kernel::class);
$request = $container->make(Request::class);

$kernel->run($request);
