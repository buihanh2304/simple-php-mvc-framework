<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

return [
    'app' => [
        'name' => env('APP_NAME', 'K-MVC'),
    ],
    'log' => [
        'path' => env('LOG_PATH', SYSTEM . 'files' . DS . 'logs'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
];
