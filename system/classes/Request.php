<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Request
{
    private $__ip;
    private $__ip_via_proxy = 0;
    private $__ip_list = array();
    private $__user_agent;
    private $__is_ajax;
    private $__is_post;

    private $__method;
    private $allowed_methods = array (
        'POST',
        'GET',
        'DELETE',
        'PUT',
        'HEAD'
    );

    private $__flood_chk = 1;
    private $__flood_interval = 120;
    private $__flood_limit = 60;

    function __construct()
    {
        $this->__get_ip();
        $this->__detect_ajax();
        $this->__ip_flood();
        $this->__get_ip_via_proxy();
        $this->__get_user_agent();
        $this->__get_method();
        $this->__detect_post();
        session_name('MRKEN_BLOG');
        session_start();
    }

    public function isset_post($name)
    {
        return isset($_POST[$name]) ? true : false;
    }

    public function isset_get($name)
    {
        return isset($_GET[$name]) ? true : false;
    }

    public function get_var($name, $default = '')
    {
        $value = '';
        if (isset($_GET[$name])) {
            $value = $this->__process_var(gettype($default), $_GET[$name], $default);
        } else {
            $value = $default;
        }
        return $value;
    }

    public function post_var($name, $default = '', $substr = 0)
    {
        $value = '';
        if (isset($_POST[$name])) {
            $value = $this->__process_var(gettype($default), $_POST[$name], $default, $substr = 0);
        } else {
            $value = $default;
        }
        return $value;
    }

    private function __process_var($type, $value, $default, $substr = 0)
    {
        switch ($type)
        {
            case 'integer':
                $value = abs(intval($value));
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

    public function get_ip()
    {
        return $this->__ip;
    }

    public function get_ip_via_proxy()
    {
        return $this->__ip_via_proxy;
    }

    public function get_user_agent()
    {
        return $this->__user_agent;
    }

    public function get_ip_list()
    {
        return $this->__ip_list;
    }

    public function is_ajax()
    {
        return $this->__is_ajax;
    }

    public function get_allowed_methods()
    {
        return $this->allowed_methods;
    }

    public function get_method()
    {
        return $this->__method;
    }

    public function is_post()
    {
        return $this->__is_post;
    }

    public function check_method($method = 'GET')
    {
        return ($method === $this->get_method());
    }

    private function __get_method()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = mb_strtoupper(trim($_SERVER['REQUEST_METHOD']));
            if ($method === 'POST' && isset($_SERVER['HTTP_X_METHOD'])) {
                $method = mb_strtoupper(trim($_SERVER['HTTP_X_METHOD']));
            }
            if (in_array($method, $this->allowed_methods)) {
                return ($this->__method = $method);
            }
        }
        die('Error: request method is not allowed!');
    }

    private function __detect_ajax()
    {
        $header = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? mb_strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) : '';
        $this->__is_ajax = ($header === 'xmlhttprequest');
    }

    private function __detect_post()
    {
        $this->__is_post = ('POST' === $this->get_method());
    }

    public function get_route()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = trim($_SERVER['REQUEST_URI']);
            $pos = mb_strpos($uri, '?');
            if ($pos !== false) {
                $uri = mb_substr($uri, 0, $pos);
            }
            return $uri;
        }
    }

    private function __get_ip()
    {
        $ip = ip2long($_SERVER['REMOTE_ADDR']) or die('Invalid IP');
        $this->__ip = sprintf('%u', $ip);
    }

    private function __get_ip_via_proxy()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $vars)) {
            foreach ($vars[0] AS $var) {
                $ip_via_proxy = ip2long($var);
                if ($ip_via_proxy && $ip_via_proxy != $this->__ip && !preg_match('#^(10|172\.16|192\.168)\.#', $var)) {
                    $this->__ip_via_proxy = sprintf('%u', $ip_via_proxy);
                    break;
                }
            }
        }
    }

    private function __get_user_agent()
    {

        if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && strlen(trim($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])) > 5) {
            $this->__user_agent = 'Opera Mini: ' . mb_substr(trim($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']), 0, 255);
        } elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->__user_agent = mb_substr(trim($_SERVER['HTTP_USER_AGENT']), 0, 255);
        } else {
            $this->__user_agent = 'Not Recognised';
        }
    }

    private function __ip_flood()
    {
        if ($this->__flood_chk) {
            $file = APP . 'files' . DS . 'cache' . DS . 'ip.dat';
            $tmp = array();
            $requests = 1;
            if (file_exists($file)) {
                $in = fopen($file, 'r+');
            } else {
                $in = fopen($file, 'w+');
            }
            flock($in, LOCK_EX) or die('Cannot flock ANTIFLOOD file.');
            while ($block = fread($in, 8)) {
                $arr = unpack('Lip/Ltime', $block);
                if ((TIME - $arr['time']) > $this->__flood_interval) {
                    continue;
                }
                if ($arr['ip'] == $this->__ip) {
                    $requests++;
                }
                $tmp[] = $arr;
                $this->__ip_list[] = $arr['ip'];
            }
            fseek($in, 0);
            ftruncate($in, 0);
            for ($i = 0; $i < count($tmp); $i++) {
                fwrite($in, pack('LL', $tmp[$i]['ip'], $tmp[$i]['time']));
            }
            fwrite($in, pack('LL', $this->__ip, TIME));
            fclose($in);
            if ($requests > $this->__flood_limit) {
                die('FLOOD: exceeded limit of allowed requests');
            }
        }
    }
}
