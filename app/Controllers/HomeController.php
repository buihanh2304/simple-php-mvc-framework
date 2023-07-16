<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
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
