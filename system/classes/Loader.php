<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

class Loader
{

    public function view()
    {
        return $this->load(Template::class);
    }

    public function controller($name)
    {
        $className = $name . 'Controller';

        return $this->load($className);
    }

    public function model($name)
    {
        $className = $name . 'Model';

        return $this->load($className);
    }

    public function library($name)
    {
        $className = $name . 'Library';

        return $this->load($className);
    }

    private function load($className)
    {
        return Container::get($className);
    }
}
