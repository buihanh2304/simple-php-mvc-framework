<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

namespace System\Classes;

class Controller
{
    protected Auth $auth;
    protected Request $request;
    protected Config $config;

    public function __construct()
    {
        $this->auth = app(Auth::class);
        $this->request = app(Request::class);
        $this->config = app(Config::class);
    }
}
