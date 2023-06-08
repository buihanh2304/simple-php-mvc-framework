<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

// default controller
define('DEFAULT_CONTROLLER', 'Home');
// URL
define('SITE_SCHEME', 'http://');
define('SITE_HOST', 'basic.pro');
define('SITE_PATH', '');
define('SITE_URL', SITE_SCHEME . SITE_HOST . SITE_PATH);
// Cookie
define('COOKIE_PATH', '/' . SITE_PATH);
// Database
define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'basic');

// Time zone
date_default_timezone_set('Asia/Ho_Chi_Minh');

ini_set('session.use_trans_sid', '0');
ini_set('arg_separator.output', '&amp;');
mb_internal_encoding('UTF-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
