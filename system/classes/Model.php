<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Model
{
    protected $load;
    protected $db;
    protected $config;

    function __construct()
    {
        $this->db = Core::get('DB');
        $this->config = Core::get('Config');
        $this->load = new Loader();
    }
}
