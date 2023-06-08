<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Router
{
    private $basePath = '';
    private $routes = array();
    private $allowed_methods = array();
    private $pattenMatchers = array(
        '/{(.*?):number}/'        => '{$1:[0-9]+?}',
        '/{(.*?):id}/'            => '{$1:[1-9][0-9]*?}',
        '/{(.*?):word}/'          => '{$1:[a-zA-Z]+?}',
        '/{(.*?):slug}/'          => '{$1:[a-z0-9-]+?}',
    );
    private $regex_delimiter = '#';

    private $pattenIndex = 0;
    private $pattenData = array();

    private $request_params = array();

    function __construct($basePath = '', array $allowed_methods = array('GET', 'POST'))
    {
        $this->basePath = $basePath;
        $this->allowed_methods = $allowed_methods;
        foreach ($this->allowed_methods as $method) {
            $this->routes[$method] = array();
        }
    }

    public function set_allowed_methods(array $methods)
    {
        $this->allowed_methods = $methods;
    }

    public function set_base_path($basePath)
    {
        $this->basePath = $basePath;
    }

    public function add($router, $handler = 'Misc@error_404', $methods = 'GET')
    {
        $patten = $this->__process_router($router);
        $handler = $this->__process_handler($handler);
        $methods = $this->__process_method($methods);
        foreach ($methods as $method) {
            $this->routes[$method][$patten] = $handler;
        }
    }

    public function match($method, $route)
    {
        if ($this->basePath) {
            $route = preg_replace(
                $this->regex_delimiter . '^' . preg_quote($this->basePath, $this->regex_delimiter) . $this->regex_delimiter,
                '',
                $route
            );
        }
        if (isset($this->routes[$method][$route])) {
            $this->request_params = array($this->routes[$method][$route], array());
            return;
        }
        foreach ($this->routes[$method] as $key => $value) {
            if (false !== mb_strpos($key, $this->regex_delimiter)) {
                if (preg_match($key, $route, $matches)) {
                    foreach ($matches as $k => $v) {
                        if (is_int($k)) {
                            unset($matches[$k]);
                        }
                    }
                    $this->request_params = array($value, $matches);
                    break;
                }
            }
        }
    }

    public function get_request_params()
    {
        if ($this->request_params) {
            list($controller, $method) = explode('@', $this->request_params[0]);
            return [
                'controller' => $controller,
                'method'     => $method,
                'params'     => $this->request_params[1]
            ];
        }
        return false;
    }

    private function __process_method($methods)
    {
        if ($methods === '*') {
            $methods = $this->allowed_methods;
            return $methods;
        }
        $return = array();
        $methods = explode('|', mb_strtoupper($methods));
        foreach ($methods as $method) {
            if (in_array($method, $this->allowed_methods)) {
                $return[] = $method;
            }
        }
        return $return;
    }

    private function __process_router($router)
    {
        if (false === mb_strpos($router, '{')) {
            return $router;
        }
        $router = preg_replace(array_keys($this->pattenMatchers), array_values($this->pattenMatchers), $router);
        $router = preg_replace_callback(
            $this->regex_delimiter . '{(.*?):(.+?)}' . $this->regex_delimiter,
            array($this, '__process_patten'),
            $router
        );
        $router = preg_quote($router, $this->regex_delimiter);
        $router = preg_replace_callback(
            $this->regex_delimiter . '@PID_(\d+)@' . $this->regex_delimiter,
            array($this, '__replace_patten'),
            $router
        );
        $router = $this->regex_delimiter . '^' . $router . '$' . $this->regex_delimiter;
        return $router;
    }

    private function __process_patten($matches)
    {
        ++$this->pattenIndex;
        if ($matches[1]) {
            $this->pattenData[$this->pattenIndex] = '(?<' . $matches[1] . '>' . $matches[2] . ')';
        } else {
            $this->pattenData[$this->pattenIndex] = $matches[2];
        }
        return '@PID_' . $this->pattenIndex . '@';
    }

    private function __replace_patten($matches) {
        if (isset($this->pattenData[$matches[1]])) {
            $data = $this->pattenData[$matches[1]];
            unset($this->pattenData[$matches[1]]);
            return $data;
        }
    }

    private function __process_handler($handler)
    {
        return $handler;
    }
}
