<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

use App\Controllers\HomeController;

/** @var \System\Classes\Router $router */

$router->add('/', 'HomeController@index');

$router->add('/home', function () {
    return view('home/main');
});

$router->add('/test/1', [HomeController::class, 'index']);
$router->add('/test/2', [HomeController::class, 'index'], ['get']);
$router->add('/api/test', function () {
    return [
        'response' => 'ok',
    ];
});

// exemple
$router->add('/register', 'UserController@register', 'GET|POST');
$router->add('/login', 'UserController@login', 'GET|POST');
$router->add('/logout', 'UserController@logout', 'GET|POST');
