<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
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
    protected Template $view;

    function __construct()
    {
        $this->load = Container::get(Loader::class);
        $this->auth = Container::get(Auth::class);
        $this->request = Container::get(Request::class);
        $this->config = Container::get(Config::class);

        $this->view = Container::get(Template::class);
    }
}
