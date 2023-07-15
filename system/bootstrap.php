<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

use Dotenv\Dotenv;
use System\Classes\Config;
use System\Classes\Container;
use System\Classes\Env;

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

// Autoload
require(ROOT . 'vendor' . DS . 'autoload.php');

// Load env
Dotenv::create(
    Env::getRepository(),
    ROOT,
    '.env'
)->safeLoad();

// Global config
require(ROOT . 'configs' . DS . 'init.php');

// Register Service Providers
$container = Container::getInstance();

$container->instance(Container::class, $container);

$config = $container->make(Config::class);
$providers = $config->get('providers');

foreach ($providers as $provider) {
    $container->make($provider)->register();
}
