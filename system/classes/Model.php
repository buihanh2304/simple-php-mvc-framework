<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

class Model
{
    protected Loader $load;
    protected PDO $db;
    protected Config $config;

    function __construct()
    {
        $this->db = Container::get('DB');
        $this->config = Container::get('Config');
        $this->load = Container::get(Loader::class);
    }
}
