<?php

use App\Providers\RouteServiceProvider;
use System\Providers\AppServiceProvider;
use System\Providers\CaptchaServiceProvider;

return [
    AppServiceProvider::class,
    RouteServiceProvider::class,
    CaptchaServiceProvider::class,
];
