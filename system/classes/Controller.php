<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

class Controller
{
    protected Loader $load;
    protected Auth $auth;
    protected Request $request;
    protected Config $config;

    function __construct()
    {
        $this->load = Container::get(Loader::class);
        $this->auth = Container::get(Auth::class);
        $this->request = Container::get(Request::class);
        $this->config = Container::get(Config::class);
    }
}
