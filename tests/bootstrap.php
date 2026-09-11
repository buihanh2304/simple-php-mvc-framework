<?php

declare(strict_types=1);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('APP', ROOT . 'app' . DS);
define('SYSTEM', ROOT . 'system' . DS);
define('TIME', time());
define('SITE_SCHEME', 'http://');
define('SITE_HOST', 'localhost');
define('SITE_PATH', '');
define('SITE_URL', SITE_SCHEME . SITE_HOST . SITE_PATH);
define('COOKIE_PATH', '/' . SITE_PATH);

require ROOT . 'vendor' . DS . 'autoload.php';

$_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (session_status() === PHP_SESSION_NONE) {
    session_save_path(sys_get_temp_dir());
    session_name('K_MVC');
    session_start();
}
