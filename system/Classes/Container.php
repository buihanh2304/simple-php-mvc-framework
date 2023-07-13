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

class Container
{
    private static $instances = [];

    public static function get($name, $params = [])
    {
        if (class_exists($name, true)) {
            $hash = $name . md5(serialize($params));

            if (!isset(self::$instances[$hash])) {
                $obj = new $name(...$params);

                if (is_callable($obj)) {
                    self::$instances[$hash] = $obj(...$params);
                } else {
                    self::$instances[$hash] = $obj;
                }
            }

            return self::$instances[$hash];
        }
    }
}
