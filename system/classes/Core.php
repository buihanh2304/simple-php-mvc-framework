<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Core
{
    private static $__container = array();

    public static function get($name, $params = array())
    {
        if (class_exists($name, true)) {
            $hash = $name . md5(serialize($params));
            if (!isset(self::$__container[$hash])) {
                $obj = new $name(...$params);
                if (is_callable($obj)) {
                    self::$__container[$hash] = $obj(...$params);
                } else {
                    self::$__container[$hash] = $obj;
                }
            }
            return self::$__container[$hash];
        }
    }
}
