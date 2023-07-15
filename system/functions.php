<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

function _e(string $text)
{
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    return trim($text);
}

/**
 * Get src of captcha image
 *
 * @return string
 */
function captchaSrc()
{
    return url('captcha') . '?v=' . time();
}
/**
 * Get autoload config
 *
 * @param string|null $path
 * @param mixed $default
 * @return Config|mixed
 */
function config(string $path = null, $default = null)
{
    $config = Container::get(Config::class);

    if (is_null($path)) {
        return $config;
    }

    return $config->get($path, $default);
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

/**
 * Generate pagination HTML
 *
 * @param string $url
 * @param int $page
 * @param int $total
 * @param int $perPage
 * @param string $suffix
 * @return string
 */
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

function redirect(string $uri = '/')
{
    header('Location: ' . SITE_PATH . $uri);
    exit;
}

/**
 * Get request instance
 *
 * @return Request
 */
function request()
{
    return Container::get(Request::class);
}

function url(string $path = '', $absulute = true)
{
    if ($absulute) {
        return SITE_URL . '/' . ltrim($path, '/');
    }

    return (SITE_PATH ? '/' . ltrim(SITE_PATH, '/') : '')
        . '/' . ltrim($path, '/');
}

/**
 * Get template instance or render a view
 *
 * @param string|null $template
 * @param array $data
 * @return Template|string
 */
function view(string $template = null, array $data = [])
{
    /** @var Template */
    $view = Container::get(Template::class);

    if (is_null($template)) {
        return $view;
    }

    return $view->render($template, $data);
}
