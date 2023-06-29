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

    public function __construct()
    {
        $configs = [];

        foreach (glob(ROOT . 'configs' . DS . 'autoload' . DS . '?*.php') as $file) {
            $configs = array_merge($configs, [
                basename($file, '.php') => include($file)
            ]);
        }

        $this->configs = $configs;
    }

    public function get($key = null, $default = null)
    {
        $result = $this->configs;

        if (is_null($key)) {
            return $result;
        }

        if (isset($result[$key])) {
            return $result[$key];
        }

        $paths = explode('.', (string) $key);

        foreach ($paths as $path) {
            if (isset($result[$path])) {
                $result = $result[$path];
            } else {
                return $default;
            }
        }

        return $result;
    }
}
