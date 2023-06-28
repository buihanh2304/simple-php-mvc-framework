<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

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
