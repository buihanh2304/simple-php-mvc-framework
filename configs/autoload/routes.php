<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

// you can add router if you want, but make sure in right form

return [
    ['/', 'Home@index'],
    [
        'home',
        function () {
            /** @var Loader */
            $loader = Container::get(Loader::class);

            $view = $loader->view();

            return $view->render('home/main');
        },
    ],

    // exemple
    ['/register', 'User@register', 'GET|POST'],
    ['/login', 'User@login', 'GET|POST'],
    ['/logout', 'User@logout', 'GET|POST'],
];
