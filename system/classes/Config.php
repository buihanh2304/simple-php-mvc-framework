<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Config
{
    private $configs;

    function __construct()
    {
        $configs = [];
        foreach (glob(APP . 'configs' . DS . 'autoload' . DS . '?*.php') as $file)
        {
            $configs = array_merge($configs, include($file));
        }
        $this->configs = $configs;
    }

    public function __call($name, $arguments)
    {
        $configs = $this->configs;
        $count = count($arguments);
        switch ($count) {
            case 2:
                if (is_string($arguments[0]) && (is_string($arguments[1]) || is_int($arguments[1]))) {
                    if (isset($configs[$name][$arguments[0]][$arguments[1]])) {
                        return $configs[$name][$arguments[0]][$arguments[1]];
                    }
                }
                break;
            case 1:
                if (is_string($arguments[0])) {
                    if (isset($configs[$name][$arguments[0]])) {
                        return $configs[$name][$arguments[0]];
                    }
                }
                break;

            case 0:
                if (isset($configs[$name])) {
                    return $configs[$name];
                }
                break;

            default:
                return null;
        }
        return null;
    }
}
