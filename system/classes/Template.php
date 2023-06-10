<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Template
{
    private $plates;

    private $global = array();
    private $data = array();

    function __construct()
    {
        $user = Core::get('User');
        $plates = new League\Plates\Engine(ROOT . 'templates');
        $plates->loadExtension(new League\Plates\Extension\Asset(ROOT . 'public', true));
        $plates->addData([
            'site_url'           => SITE_URL,
            'site_path'          => SITE_PATH,
            'isLogin'            => $user->isLogin,
            'user'               => $user->data,
            'rights'             => $user->rights
        ]);
        // Load extensions
        $this->plates = $plates;
    }

    public function setTitle($title)
    {
        $this->addGlobal('page_title', _e($title));
    }

    public function addGlobal($name, $value = '')
    {
        $data = $this->__process_data($name, $value);
        $this->global = array_merge($this->global, $data);
    }

    public function addData($name, $value = '')
    {
        $data = $this->__process_data($name, $value);
        $this->data = array_merge($this->data, $data);
    }

    private function __process_data($name, $value)
    {
        $data = array();
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $data[$key] = $val;
            }
        } else {
            $data[$name] = $value;
        }
        return $data;
    }

    public function render($file, $data = array())
    {
        if (!empty($this->global)) {
            $this->plates->addData($this->global);
        }
        $this->data = array_merge($this->data, $data);

        return $this->plates->render($file, $this->data);
    }

    public function output($file, $data = array())
    {
        echo $this->render($file, $data);
    }
}
