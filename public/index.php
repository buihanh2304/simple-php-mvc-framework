<?php
define('_MRKEN_MVC', 1);

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

require('../system/bootstrap.php');

$request = Core::get('Request');

$kernel = Core::get('Kernel');

$kernel->run($request);
