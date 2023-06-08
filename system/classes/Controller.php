<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Controller
{
    protected $load;
    protected $request;
    protected $config;

    function __construct()
    {
        $this->load = new Loader();
        $this->request = Core::get('Request');
        $this->config = Core::get('Config');
    }
}
