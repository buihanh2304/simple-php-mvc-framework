<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

function config(string $path)
{
    $config = Container::get('Config');
    $paths = explode('.', $path, 2);

    if (isset($paths[1])) {
        return $config->{$paths[0]}($paths[1]);
    }

    return $config->{$paths[0]}();
}

function url($path = '', $absulute = true)
{
    if ($absulute) {
        return SITE_URL . '/' . ltrim($path, '/');
    }

    return (SITE_PATH ? '/' . ltrim(SITE_PATH, '/') : '')
        . '/' . ltrim($path, '/');
}

function captchaSrc()
{
    return url('captcha') . '?v=' . time();
}

function redirect($uri = '/')
{
    header('Location: ' . SITE_PATH . $uri);
    exit;
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

function pagination($url, &$page, $total, $perPage, $suffix = '')
{
    $neighbors = 2;

    if ($page < 1) {
        $page = 1;
    }

    if ($total <= $perPage) {
        $page = 1;
        return;
    }

    $max_page = ceil($total / $perPage);

    if ($page > $max_page) {
        $page = $max_page;
    }

    $out = [];

    $base_link = '<li class="page-item"><a class="page-link" href="' . SITE_PATH . strtr($url, ['%' => '%%']) . '%d' . $suffix . '">%s</a></li>';

    $out[] = $page == 1
        ? '<li class="page-item disabled"><span class="page-link">&laquo;</span></li><li class="page-item disabled"><span class="page-link">&lt;</span></span></li>'
        : sprintf($base_link, 1, '&laquo;') . sprintf($base_link, $page - 1, '&lt;');

    for ($i = 2 * $neighbors; $i >= $neighbors + 1; $i--) {
        if ($page - $i >= 1 && $page + 2 * $neighbors - $i + 1 > $max_page) {
            $tmpPage = $page - $i;
            $out[] = sprintf($base_link, $tmpPage, $tmpPage);
        }
    }
    for ($i = $neighbors; $i >= 1; $i--) {
        if ($page - $i >= 1) {
            $tmpPage = $page - $i;
            $out[] = sprintf($base_link, $tmpPage, $tmpPage);
        }
    }
    $out[] = '<li class="page-item active"><span class="page-link">' . $page . '</span></li>';
    for ($i = 1; $i <= $neighbors; $i++) {
        if ($page + $i <= $max_page) {
            $tmpPage = $page + $i;
            $out[] = sprintf($base_link, $tmpPage, $tmpPage);
        }
    }
    for ($i = $neighbors + 1; $i <= 2 * $neighbors; $i++) {
        if ($page + $i <= $max_page && $page - 2 * $neighbors + $i - 1 < 1) {
            $tmpPage = $page + $i;
            $out[] = sprintf($base_link, $tmpPage, $tmpPage);
        }
    }
    if ($page < $max_page) {
        $out[] = sprintf($base_link, $page + 1, '&gt;') . sprintf($base_link, $max_page, '&raquo;');
    } else {
        $out[] = '<li class="page-item disabled"><span class="page-link">&gt;</span></li><li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
    }

    return '<nav><ul class="pagination justify-content-center text-center mb-0">' . implode('', $out) . '</ul></nav>';
}
