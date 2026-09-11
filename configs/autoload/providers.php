<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

use System\Providers\AppServiceProvider;
use System\Providers\CaptchaServiceProvider;
use System\Providers\RouteServiceProvider;

return [
    AppServiceProvider::class,
    RouteServiceProvider::class,
    CaptchaServiceProvider::class,
];
