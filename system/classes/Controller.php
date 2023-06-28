<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

class Controller
{
    protected Loader $load;
    protected Auth $auth;
    protected Request $request;
    protected Config $config;
    protected Template $view;

    function __construct()
    {
        $this->load = new Loader();
        $this->auth = Container::get('Auth');
        $this->request = Container::get('Request');
        $this->config = Container::get('Config');
        $this->view = $this->load->view();
    }

    public function notFound()
    {
        $this->view->setTitle('404 Not Found');
        header('HTTP/1.1 404 Not Found', true, 404);
        $this->view->output('home/error_404');
    }
}
