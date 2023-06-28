<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

class Config
{
    private $configs;

    function __construct()
    {
        $configs = [];

        foreach (glob(ROOT . 'configs' . DS . 'autoload' . DS . '?*.php') as $file) {
            $configs = array_merge($configs, include($file));
        }

        $this->configs = $configs;
    }

    public function __call($name, $arguments)
    {
        $result = $this->configs[$name];

        if (count($arguments)) {
            $key = $arguments[0];

            if (isset($result[$key])) {
                return $result[$key];
            }

            $paths = explode('.', (string) $key);

            foreach ($paths as $path) {
                if (isset($result[$path])) {
                    $result = $result[$path];
                } else {
                    return null;
                }
            }
        }

        return $result;
    }
}
