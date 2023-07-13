<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

namespace System\Classes;

use League\Plates\Engine;
use League\Plates\Extension\Asset;

class Template
{
    private Engine $plates;

    private $global = [];
    private $data = [];

    public function __construct(Auth $auth, Engine $engine)
    {
        $engine->setDirectory(ROOT . 'templates');
        $engine->loadExtension(new Asset(ROOT . 'public', true));
        $engine->addData([
            'isLogin'            => $auth->isLogin,
            'user'               => $auth->user,
            'rights'             => $auth->rights
        ]);

        // Load extensions
        $this->plates = $engine;
    }

    public function getEngine(): Engine
    {
        return $this->plates;
    }

    public function setTitle($title)
    {
        $this->addGlobal('page_title', _e($title));
    }

    public function addGlobal($name, $value = '')
    {
        $data = $this->processData($name, $value);
        $this->global = array_merge($this->global, $data);
    }

    public function addData($name, $value = '')
    {
        $data = $this->processData($name, $value);
        $this->data = array_merge($this->data, $data);
    }

    private function processData($name, $value)
    {
        $data = [];

        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $data[$key] = $val;
            }
        } else {
            $data[$name] = $value;
        }

        return $data;
    }

    public function render($file, $data = [])
    {
        if (!empty($this->global)) {
            $this->plates->addData($this->global);
        }
        $this->data = array_merge($this->data, $data);

        return $this->plates->render($file, $this->data);
    }

    public function output($file, $data = [])
    {
        echo $this->render($file, $data);
    }
}
