<?php
define('_MRKEN_MVC', 1);

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

require('../system/bootstrap.php');

/** @var Kernel */
$kernel = Container::get('Kernel');

$kernel->run(Container::get('Request'));
