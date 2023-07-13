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

use PDO;

class Model
{
    protected PDO $db;
    protected Config $config;

    public function __construct()
    {
        $this->db = app(DB::class);
        $this->config = app(Config::class);
    }
}
