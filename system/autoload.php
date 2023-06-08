<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

// Autoload class
function autoload($name) {
    if (preg_match('#[^a-z]#i', $name)) {
        return;
    }
    if (preg_match('/^([a-z]+)Controller$/i', $name, $matches)) {
        $file = APP . 'controller' . DS . $matches[1] . '.php';
    } elseif (preg_match('/^([a-z]+)Model$/i', $name, $matches)) {
        $file = APP . 'model' . DS . $matches[1] . '.php';
    } elseif (preg_match('/^([a-z]+)Library$/i', $name, $matches)) {
        $file = APP . 'library' . DS . $matches[1] . '.php';
    } else {
        $file = APP . 'classes' . DS . $name . '.php';
    }
    if (file_exists($file)) {
        require_once($file);
    }
}

spl_autoload_register('autoload');
