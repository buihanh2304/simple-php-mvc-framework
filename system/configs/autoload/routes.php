<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

// you can add router if you want, but make sure in right form

return [
    'routes' => [
        ['/', 'Home@index'],
        // exemple
        ['/register', 'User@register', 'GET|POST'],
        ['/login', 'User@login', 'GET|POST'],
        ['/logout', 'User@logout', 'GET|POST'],
    ]
];
