<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

// Autoload class
function autoload($name) {
    if (preg_match('#[^a-z]#i', $name)) {
        return;
    }

    if (preg_match('/^([a-z]+)Controller$/i', $name, $matches)) {
        $file = APP . 'controllers' . DS . $matches[1] . '.php';
    } elseif (preg_match('/^([a-z]+)Model$/i', $name, $matches)) {
        $file = APP . 'models' . DS . $matches[1] . '.php';
    } elseif (preg_match('/^([a-z]+)Library$/i', $name, $matches)) {
        $file = APP . 'libraries' . DS . $matches[1] . '.php';
    } elseif (preg_match('/^([a-z]+)Service$/i', $name, $matches)) {
        $file = APP . 'services' . DS . $name . '.php';

        if (!file_exists($file)) {
            $file = SYSTEM . 'services' . DS . $name . '.php';
        }
    } elseif (preg_match('/^([a-z]+)Interface$/i', $name, $matches)) {
        $file = APP . 'interfaces' . DS . $name . '.php';

        if (!file_exists($file)) {
            $file = SYSTEM . 'interfaces' . DS . $name . '.php';
        }
    } else {
        $file = SYSTEM . 'classes' . DS . $name . '.php';
    }

    if (file_exists($file)) {
        require_once($file);
    }
}

spl_autoload_register('autoload');
