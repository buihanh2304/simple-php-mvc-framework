<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

// URL
define('SITE_SCHEME', env('SITE_SCHEME', 'http://'));
define('SITE_HOST', env('SITE_HOST', 'localhost'));
define('SITE_PATH', env('SITE_PATH', ''));
define('SITE_URL', SITE_SCHEME . SITE_HOST . SITE_PATH);
// Cookie
define('COOKIE_PATH', '/' . SITE_PATH);
// Database
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', 'root'));
define('DB_NAME', env('DB_NAME', 'mvc'));

// Time zone
date_default_timezone_set('Asia/Ho_Chi_Minh');

ini_set('session.use_trans_sid', '0');
ini_set('arg_separator.output', '&amp;');
mb_internal_encoding('UTF-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

ini_set('session.cookie_httponly', 1);

if (extension_loaded('zlib')) {
    ini_set('zlib.output_compression', 'On');
    ini_set('zlib.output_compression_level', 3);
}
