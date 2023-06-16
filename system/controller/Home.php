<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class HomeController extends Controller
{
    public function error_404()
    {
        $this->notFound();
    }

    public function index()
    {
        echo $this->view->render('home/main');
    }
}
