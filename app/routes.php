<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

/** @var Router */
$router = Container::get(Router::class);

$router->add('/', 'HomeController@index');

$router->add('/home', function () {
    /** @var Template */
    $view = Container::get(Template::class);

    return $view->render('home/main');
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
