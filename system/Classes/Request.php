<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

namespace System\Classes;

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
    private $floodRecordSize = 20;

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

    public function user(): Auth
    {
        return app(Auth::class);
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
            $value = $this->processVar(gettype($default), $_POST[$name], $default, $substr);
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
        $this->ip = $this->normalizeIp($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        if ($this->ip === false) {
            die('Invalid IP');
        }
    }

    private function processIpViaProxy()
    {
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return;
        }

        foreach ($this->parseForwardedIps($_SERVER['HTTP_X_FORWARDED_FOR']) as $ip) {
            if ($ip !== $this->ip && $this->isPublicIp($ip)) {
                $this->ipViaProxy = $ip;

                break;
            }
        }
    }

    /**
     * @return list<string>
     */
    private function parseForwardedIps(string $header): array
    {
        $ips = [];

        foreach (explode(',', $header) as $part) {
            $part = trim($part);

            if ($part === '') {
                continue;
            }

            if (preg_match('/^\[(.+)\](?::\d+)?$/', $part, $matches)) {
                $part = $matches[1];
            } elseif (preg_match('/^(\d{1,3}(?:\.\d{1,3}){3}):\d+$/', $part, $matches)) {
                $part = $matches[1];
            }

            $ip = $this->normalizeIp($part);

            if ($ip !== false) {
                $ips[] = $ip;
            }
        }

        return $ips;
    }

    /**
     * @return string|false
     */
    private function normalizeIp($ip)
    {
        $ip = trim((string) $ip);

        if ($ip !== '' && $ip[0] === '[' && substr($ip, -1) === ']') {
            $ip = substr($ip, 1, -1);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        $binary = inet_pton($ip);

        if ($binary === false) {
            return false;
        }

        if (strlen($binary) === 16 && strncmp($binary, "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff", 12) === 0) {
            $binary = substr($binary, 12);
        }

        $normalized = inet_ntop($binary);

        return $normalized === false ? false : $normalized;
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    private function processUserAgent()
    {
        if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && mb_strlen(trim($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])) > 5) {
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

            $ipBinary = $this->ipToBinary();
            clearstatcache(true, $file);

            if (filesize($file) % $this->floodRecordSize === 0) {
                while ($block = fread($in, $this->floodRecordSize)) {
                    if (strlen($block) !== $this->floodRecordSize) {
                        break;
                    }

                    $storedIp = substr($block, 0, 16);
                    $time = unpack('Ltime', substr($block, 16, 4))['time'];

                    if ((TIME - $time) > $this->floodInterval) {
                        continue;
                    }

                    if ($storedIp === $ipBinary) {
                        $requests++;
                    }

                    $tmp[] = ['ip' => $storedIp, 'time' => $time];
                    $this->ipList[] = $this->binaryToIp($storedIp);
                }
            }

            fseek($in, 0);
            ftruncate($in, 0);

            for ($i = 0; $i < count($tmp); $i++) {
                fwrite($in, $tmp[$i]['ip'] . pack('L', $tmp[$i]['time']));
            }

            fwrite($in, $ipBinary . pack('L', TIME));
            fclose($in);

            if ($requests > $this->floodLimit) {
                die('FLOOD: exceeded limit of allowed requests');
            }
        }
    }

    private function ipToBinary(): string
    {
        $binary = inet_pton((string) $this->ip);

        if ($binary === false) {
            $binary = inet_pton('0.0.0.0');
        }

        if (strlen($binary) === 4) {
            return str_repeat("\x00", 10) . "\xff\xff" . $binary;
        }

        return $binary;
    }

    private function binaryToIp(string $binary): string
    {
        if (strlen($binary) === 16 && strncmp($binary, "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff", 12) === 0) {
            $binary = substr($binary, 12);
        }

        $ip = inet_ntop($binary);

        return $ip === false ? '' : $ip;
    }
}
