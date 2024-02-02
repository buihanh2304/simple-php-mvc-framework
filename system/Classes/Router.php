<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

namespace System\Classes;

class Router
{
    private $basePath = '';

    private $routes = [];

    protected $namespace;

    private $allowedMethods = [];

    private $patternMatchers = [
        '/{:(number|id|word|slug)}/' => '{$1:$1}',
        '/{(.+?):number}/' => '{$1:[0-9]+?}',
        '/{(.+?):id}/' => '{$1:[1-9][0-9]*?}',
        '/{(.+?):word}/' => '{$1:[a-zA-Z]+?}',
        '/{(.+?):slug}/' => '{$1:[a-z0-9-]+?}',
        '/{([^:]+?)}/' => '{$1:[^/]+?}',
    ];

    private $regexDelimiter = '#';

    private $patternIndex = 0;

    private $patternData = [];

    private $requestParams = [];

    public function __construct($basePath = '', array $allowedMethods = ['GET', 'POST'])
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

    public function add(string $router, $handler, $methods = 'GET')
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
                $this->regexDelimiter . '^' . preg_quote($this->basePath, $this->regexDelimiter) . $this->regexDelimiter,
                '',
                $route
            );
        }

        if (isset($this->routes[$method][$route])) {
            $this->requestParams = [$this->routes[$method][$route], []];

            return;
        }

        foreach ($this->routes[$method] as $key => $value) {
            if (false !== mb_strpos($key, $this->regexDelimiter)) {
                if (preg_match($key, $route, $matches)) {
                    foreach ($matches as $k => $v) {
                        if (is_int($k)) {
                            unset($matches[$k]);
                        }
                    }

                    $this->requestParams = [$value, $matches];

                    break;
                }
            }
        }
    }

    public function getRequestParams()
    {
        if ($this->requestParams) {
            if (is_callable($this->requestParams[0])) {
                $callback = $this->requestParams[0];
            } else {
                if (is_array($this->requestParams[0])) {
                    list($controller, $method) = $this->requestParams[0];
                } else {
                    list($controller, $method) = explode('@', $this->requestParams[0]);
                }

                $callback = compact('controller', 'method');
            }

            return [
                'callback' => $callback,
                'params' => $this->requestParams[1]
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
        $methods = is_array($methods) ? array_map('mb_strtoupper', $methods) : explode('|', mb_strtoupper($methods));

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
            $this->regexDelimiter . '{(.*?):(.+?)}' . $this->regexDelimiter,
            [$this, 'processPattern'],
            $router
        );
        $router = preg_quote($router, $this->regexDelimiter);
        $router = preg_replace_callback(
            $this->regexDelimiter . '@PID_(\d+)@' . $this->regexDelimiter,
            [$this, 'replacePattern'],
            $router
        );
        $router = $this->regexDelimiter . '^' . $router . '$' . $this->regexDelimiter;

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
        if (is_callable($handler) || is_array($handler)) {
            return $handler;
        }

        if ($this->namespace) {
            return $this->namespace . $handler;
        }

        return $handler;
    }

    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }
}
