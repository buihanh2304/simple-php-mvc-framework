<?php

use App\Controllers\HomeController;
use System\Classes\Router;

/** @var Router $router */

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
