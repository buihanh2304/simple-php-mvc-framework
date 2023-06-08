<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)) . DS);
define('APP', ROOT . 'system' . DS);

if (version_compare(PHP_VERSION, '5.6', '<')) {
    // If the version below 5.6, we stops the script and displays an error
    die('<div style="text-align: center; font-size: xx-large">'
        . '<h3 style="color: #dd0000">ERROR: outdated version of PHP</h3>'
        . 'Your needs PHP 5.6 or higher'
        . '</div>');
}

// Global config
require(APP . 'configs' . DS . 'init.php');
define('TIME', time());
// Autoload
require(APP . 'autoload.php');
require(ROOT . 'vendor' . DS . 'autoload.php');
// global function
require(APP . 'functions.php');

if (extension_loaded('zlib')) {
    ini_set('zlib.output_compression', 'On');
    ini_set('zlib.output_compression_level', 3);
}
