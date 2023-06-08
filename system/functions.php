<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

function redirect($uri = '/')
{
    header('Location: ' . SITE_PATH . $uri); exit;
}

function _e($text)
{
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return trim($text);
}

function display_error($error)
{
    if (is_array($error)) {
        if (sizeof($error) === 1) {
            $error = array_pop($error);
        } else {
            $error = '- ' . implode('<br />- ', $error);
        }
    }
    return $error;
}
