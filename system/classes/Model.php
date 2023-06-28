<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
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
        $this->load = new Loader();
    }
}
