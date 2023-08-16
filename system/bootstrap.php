<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)) . DS);
define('APP', ROOT . 'app' . DS);
define('SYSTEM', ROOT . 'system' . DS);
define('TIME', time());

if (version_compare(PHP_VERSION, '8.0', '<')) {
    // If the version below 8.0, we stops the script and displays an error
    die('<div style="text-align: center; font-size: xx-large">'
        . '<h3 style="color: #dd0000">ERROR: outdated version of PHP</h3>'
        . 'Your needs PHP 8.0 or higher'
        . '</div>');
}

// Global config
require(ROOT . 'configs' . DS . 'init.php');

// Autoload
require(SYSTEM . 'autoload.php');
require(ROOT . 'vendor' . DS . 'autoload.php');

// global function
require(SYSTEM . 'functions.php');

// Register Services
$config = Container::get(Config::class);
$services = $config->get('services');

foreach ($services as $service) {
    Container::get($service)->register();
}
