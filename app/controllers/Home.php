<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

class HomeController extends Controller
{
    public function index()
    {
        return $this->view->render('home/main');
    }
}
