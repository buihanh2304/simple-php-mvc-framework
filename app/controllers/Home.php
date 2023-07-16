<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

class HomeController extends Controller
{
    public function index()
    {
        return view('home/main');
    }
}
