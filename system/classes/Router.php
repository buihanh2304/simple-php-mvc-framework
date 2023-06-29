<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

class Router
{
    private $basePath = '';
    private $routes = [];
    private $allowedMethods = [];
    private $patternMatchers = [
        '/{:(number|id|word|slug)}/' => '{$1:$1}',
        '/{(.+?):number}/' => '{$1:[0-9]+?}',
        '/{(.+?):id}/' => '{$1:[1-9][0-9]*?}',
        '/{(.+?):word}/' => '{$1:[a-zA-Z]+?}',
        '/{(.+?):slug}/' => '{$1:[a-z0-9-]+?}',
        '/{([^:]+?)}/' => '{$1:[^/]+?}',
    ];
    private $regex_delimiter = '#';

    private $patternIndex = 0;
    private $patternData = [];

    private $request_params = [];

    function __construct($basePath = '', array $allowedMethods = ['GET', 'POST'])
    {
        $this->basePath = $basePath;
        $this->allowedMethods = $allowedMethods;

        foreach ($this->allowedMethods as $method) {
            $this->routes[$method] = [];
        }
    }

    public function setAllowedMethods(array $methods)
    {
        $this->allowedMethods = $methods;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    public function add($router, $handler = 'Home@notFound', $methods = 'GET')
    {
        $pattern = $this->processRouter($router);
        $handler = $this->processHandler($handler);
        $methods = $this->processMethod($methods);

        foreach ($methods as $method) {
            $this->routes[$method][$pattern] = $handler;
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
            $this->request_params = [$this->routes[$method][$route], []];

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

                    $this->request_params = [$value, $matches];

                    break;
                }
            }
        }
    }

    public function getRequestParams()
    {
        if ($this->request_params) {
            if (is_callable($this->request_params[0])) {
                $callback = $this->request_params[0];
            } else {
                list($controller, $method) = explode('@', $this->request_params[0]);
                $callback = compact('controller', 'method');
            }

            return [
                'callback' => $callback,
                'params' => $this->request_params[1]
            ];
        }

        return false;
    }

    private function processMethod($methods)
    {
        if ($methods === '*') {
            $methods = $this->allowedMethods;

            return $methods;
        }

        $return = [];
        $methods = explode('|', mb_strtoupper($methods));

        foreach ($methods as $method) {
            if (in_array($method, $this->allowedMethods)) {
                $return[] = $method;
            }
        }

        return $return;
    }

    private function processRouter($router)
    {
        $router = $router === '/' ? $router : trim($router, '/');

        if (false === mb_strpos($router, '{')) {
            return $router;
        }

        $router = preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $router);
        $router = preg_replace_callback(
            $this->regex_delimiter . '{(.*?):(.+?)}' . $this->regex_delimiter,
            [$this, 'processPattern'],
            $router
        );
        $router = preg_quote($router, $this->regex_delimiter);
        $router = preg_replace_callback(
            $this->regex_delimiter . '@PID_(\d+)@' . $this->regex_delimiter,
            [$this, 'replacePattern'],
            $router
        );
        $router = $this->regex_delimiter . '^' . $router . '$' . $this->regex_delimiter;

        return $router;
    }

    private function processPattern($matches)
    {
        ++$this->patternIndex;
        $this->patternData[$this->patternIndex] = '(?<' . $matches[1] . '>' . $matches[2] . ')';

        return '@PID_' . $this->patternIndex . '@';
    }

    private function replacePattern($matches)
    {
        if (isset($this->patternData[$matches[1]])) {
            $data = $this->patternData[$matches[1]];
            unset($this->patternData[$matches[1]]);

            return $data;
        }
    }

    private function processHandler($handler)
    {
        return $handler;
    }
}
