<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

class Model
{
    protected Loader $load;
    protected PDO $db;
    protected Config $config;

    function __construct()
    {
        $this->db = Container::get(DB::class);
        $this->config = Container::get(Config::class);
        $this->load = Container::get(Loader::class);
    }
}
