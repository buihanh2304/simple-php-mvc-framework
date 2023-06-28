<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 1.0
// author: MrKen
// website: https://vdevs.net
*/

class Request
{
    private $ip;
    private $ipViaProxy = 0;
    private $ipList = [];
    private $userAgent;
    private $isAjax;
    private $isPost;

    private $requestMethod;
    private $allowedMethods = [
        'POST',
        'GET',
        'DELETE',
        'PUT',
        'HEAD',
    ];

    private $floodCheck = 1;
    private $floodInterval = 60;
    private $floodLimit = 60;

    public function __construct()
    {
        $this->processIp();
        $this->detectAjax();
        $this->ipFlood();
        $this->processIpViaProxy();
        $this->processUserAgent();
        $this->processMethod();
        $this->detectPost();

        session_name('K_MVC');
        session_start();
    }

    public function issetPost($name)
    {
        return isset($_POST[$name]) ? true : false;
    }

    public function issetGet($name)
    {
        return isset($_GET[$name]) ? true : false;
    }

    public function getVar($name, $default = '')
    {
        $value = '';
        if (isset($_GET[$name])) {
            $value = $this->processVar(gettype($default), $_GET[$name], $default);
        } else {
            $value = $default;
        }
        return $value;
    }

    public function postVar($name, $default = '', $substr = 0)
    {
        $value = '';

        if (isset($_POST[$name])) {
            $value = $this->processVar(gettype($default), $_POST[$name], $default, $substr = 0);
        } else {
            $value = $default;
        }

        return $value;
    }

    private function processVar($type, $value, $default, $substr = 0)
    {
        switch ($type) {
            case 'integer':
                $value = intval($value);
                break;

            case 'boolean':
                $value = boolval($value);
                break;

            case 'array':
                $value = is_array($value) ? $value : $default;
                break;

            case 'string':
                $value = preg_replace('/[^\P{C}\n]+/u', '', $value);
                $value = trim($value);

                if ($substr) {
                    $value = trim(mb_substr($value, 0, 255));
                }

                break;

            default:
                $value = '';
        }

        return $value;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getIpViaProxy()
    {
        return $this->ipViaProxy;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function getIpList()
    {
        return $this->ipList;
    }

    public function isAjax()
    {
        return $this->isAjax;
    }

    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    public function getMethod()
    {
        return $this->requestMethod;
    }

    public function isPost()
    {
        return $this->isPost;
    }

    public function checkMethod($method = 'GET')
    {
        return mb_strtoupper($method) === $this->getMethod();
    }

    private function processMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = mb_strtoupper(trim($_SERVER['REQUEST_METHOD']));

            if ($method === 'POST' && isset($_SERVER['HTTP_X_METHOD'])) {
                $method = mb_strtoupper(trim($_SERVER['HTTP_X_METHOD']));
            }

            if (in_array($method, $this->allowedMethods)) {
                $this->requestMethod = $method;

                return;
            }
        }

        die('Error: request method is not allowed!');
    }

    private function detectAjax()
    {
        $header = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? mb_strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) : '';
        $this->isAjax = ($header === 'xmlhttprequest');
    }

    private function detectPost()
    {
        $this->isPost = ('POST' === $this->getMethod());
    }

    public function getRoute()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = trim($_SERVER['REQUEST_URI']);
            $pos = mb_strpos($uri, '?');

            if ($pos !== false) {
                $uri = mb_substr($uri, 0, $pos);
            }

            return $uri === '/' ? $uri : trim($uri, '/');
        }
    }

    private function processIp()
    {
        $ip = ip2long($_SERVER['REMOTE_ADDR']) or die('Invalid IP');
        $this->ip = sprintf('%u', $ip);
    }

    private function processIpViaProxy()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $vars)) {
            foreach ($vars[0] as $var) {
                $ipViaProxy = ip2long($var);

                if ($ipViaProxy && $ipViaProxy != $this->ip && !preg_match('#^(10|172\.16|192\.168)\.#', $var)) {
                    $this->ipViaProxy = sprintf('%u', $ipViaProxy);

                    break;
                }
            }
        }
    }

    private function processUserAgent()
    {
        if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && strlen(trim($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])) > 5) {
            $this->userAgent = 'Opera Mini: ' . mb_substr(trim($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']), 0, 255);
        } elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->userAgent = mb_substr(trim($_SERVER['HTTP_USER_AGENT']), 0, 255);
        } else {
            $this->userAgent = 'Not Recognised';
        }
    }

    private function ipFlood()
    {
        if ($this->floodCheck) {
            $file = SYSTEM . 'files' . DS . 'cache' . DS . 'ip.dat';
            $tmp = [];
            $requests = 1;

            if (file_exists($file)) {
                $in = fopen($file, 'r+');
            } else {
                $in = fopen($file, 'w+');
            }

            flock($in, LOCK_EX) or die('Cannot flock ANTIFLOOD file.');

            while ($block = fread($in, 8)) {
                $arr = unpack('Lip/Ltime', $block);

                if ((TIME - $arr['time']) > $this->floodInterval) {
                    continue;
                }

                if ($arr['ip'] == $this->ip) {
                    $requests++;
                }

                $tmp[] = $arr;
                $this->ipList[] = $arr['ip'];
            }

            fseek($in, 0);
            ftruncate($in, 0);

            for ($i = 0; $i < count($tmp); $i++) {
                fwrite($in, pack('LL', $tmp[$i]['ip'], $tmp[$i]['time']));
            }

            fwrite($in, pack('LL', $this->ip, TIME));
            fclose($in);

            if ($requests > $this->floodLimit) {
                die('FLOOD: exceeded limit of allowed requests');
            }
        }
    }
}
