<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

return [
    'system' => [
        'app' => [
            'name' => 'K-MVC',
        ],
    ],
    'services' => [
        CaptchaService::class,
    ],
];
