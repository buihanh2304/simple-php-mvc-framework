<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

namespace App\Controllers;

use System\Classes\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('home/main');
    }
}
