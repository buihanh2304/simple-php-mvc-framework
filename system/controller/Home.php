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
    private $view;

    function __construct()
    {
        parent::__construct();
        $this->view = $this->load->view();
    }

    public function error_404()
    {
        $this->view->setTitle('404 Not Found');
        header('HTTP/1.1 404 Not Found', true, 404);
        echo $this->view->render('home/error_404');
        exit;
    }

    public function index()
    {
        echo $this->view->render('home/main');
    }
}
