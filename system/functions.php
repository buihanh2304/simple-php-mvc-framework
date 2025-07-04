<?php
declare(strict_types=1);

use System\Classes\Config;
use System\Classes\Container;
use System\Classes\Env;
use System\Classes\Request;
use System\Classes\Template;

if (!function_exists('_e')) {
    function _e(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return trim($text);
    }
}

if (!function_exists('app')) {
    /**
     * Get container or a class instance
     *
     * @param mixed $abstract
     * @param array $parameters
     * @return Container|mixed
     */
    function app(mixed $abstract = null, array $parameters = []): mixed
    {
        $container = Container::getInstance();
        if ($abstract) {
            return $container->make($abstract, $parameters);
        }
        return $container;
    }
}

if (!function_exists('captchaSrc')) {
    /**
     * Get src of captcha image
     *
     * @return string
     */
    function captchaSrc(): string
    {
        return url('captcha') . '?v=' . time();
    }
}

if (!function_exists('config')) {
    /**
     * Get autoload config
     *
     * @param string|null $path
     * @param mixed $default
     * @return Config|mixed
     */
    function config(?string $path = null, mixed $default = null): mixed
    {
        $config = app(Config::class);
        if (is_null($path)) {
            return $config;
        }
        return $config->get($path, $default);
    }
}

if (!function_exists('display_error')) {
    function display_error(array|string $error): string
    {
        if (is_array($error)) {
            if (sizeof($error) === 1) {
                $error = array_pop($error);
            } else {
                $error = '- ' . implode('<br />- ', $error);
            }
        }
        return (string)$error;
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('pagination')) {
    /**
     * Generate pagination HTML
     *
     * @param string $url
     * @param int $page
     * @param int $total
     * @param int $perPage
     * @param string $suffix
     * @return string|null
     */
    function pagination(string $url, int &$page, int $total, int $perPage, string $suffix = ''): ?string
    {
        $neighbors = 2;
        if ($page < 1) {
            $page = 1;
        }
        if ($total <= $perPage) {
            $page = 1;
            return null;
        }
        $max_page = (int)ceil($total / $perPage);
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
}

if (!function_exists('redirect')) {
    function redirect(string $uri = '/'): void
    {
        header('Location: ' . SITE_PATH . $uri);
        exit;
    }
}

if (!function_exists('request')) {
    /**
     * Get request instance
     *
     * @return Request
     */
    function request(): Request
    {
        return app(Request::class);
    }
}

if (!function_exists('url')) {
    function url(string $path = '', bool $absulute = true): string
    {
        if ($absulute) {
            return SITE_URL . '/' . ltrim($path, '/');
        }
        return (SITE_PATH ? '/' . ltrim(SITE_PATH, '/') : '')
            . '/' . ltrim($path, '/');
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    function value(mixed $value, mixed ...$args): mixed
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (!function_exists('view')) {
    /**
     * Get template instance or render a view
     *
     * @param string|null $template
     * @param array $data
     * @return Template|string
     */
    function view(?string $template = null, array $data = []): Template|string
    {
        /** @var Template */
        $view = app(Template::class);
        if (is_null($template)) {
            return $view;
        }
        return $view->render($template, $data);
    }
}
