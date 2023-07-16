<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

class Loader
{
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
