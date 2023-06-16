<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class Auth
{
    public $id      = 0;
    public $rights  = 0;
    public $isLogin = 0;
    public $data = array(
        'id'                => 0,
        'account'           => '',
        'password'          => '',
        'email'             => '',
        'join_date'         => '',
        'rights'            => 0,
        'last_login'        => 0,
        'name'              => ''
    );

    public $settings;

    private $db;

    function __construct()
    {
        $this->db = Core::get('DB');
        $this->Authorize();
    }

    private function Authorize()
    {
        $id = 0;
        $password = '';
        if (isset($_SESSION['uid']) && isset($_SESSION['ups'])) {
            $id = intval(trim($_SESSION['uid']));
            $password = trim($_SESSION['ups']);
        } elseif (isset($_COOKIE['cuid']) && isset($_COOKIE['cups'])) {
            $id = intval(base64_decode(trim($_COOKIE['cuid'])));
            $password = md5(trim($_COOKIE['cups']));
            $_SESSION['uid'] = $id;
            $_SESSION['ups'] = $password;
        }
        if ($id && $password) {
            $stmt = $this->db->prepare('SELECT * FROM `users` WHERE `id` = ? LIMIT 1');
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            if ($user) {
                if ($password === $user['password']) {
                    $this->isLogin = 1;
                    $this->id = (int) $user['id'];
                    $this->rights = (int) $user['rights'];
                    $this->data = $user;
                    $this->db->prepare('UPDATE `users` SET
                        `last_login`   = ?
                        WHERE `id` = ? LIMIT 1
                    ')->execute([TIME, $user['id']]);
                } else {
                    $this->_unset();
                }
            } else {
                $this->_unset();
            }
        }
    }

    private function _unset()
    {
        unset($_SESSION['uid']);
        unset($_SESSION['ups']);
        setcookie('cuid', '', TIME - 60, COOKIE_PATH);
        setcookie('cups', '', TIME - 60, COOKIE_PATH);
    }
}
