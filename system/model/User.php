<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class UserModel extends Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function logout()
    {
        setcookie('cuid', '', TIME - 60, COOKIE_PATH);
        setcookie('cups', '', TIME - 60, COOKIE_PATH);
        unset($_SESSION['uid']);
        unset($_SESSION['ups']);
    }

    // check if user exits for login
    public function get_user_login($type, $email)
    {
        $stmt = $this->db->prepare('SELECT `id`, `password` FROM `users` WHERE `' . $type . '` = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // check if account or email is exists for register
    public function check_used_info($account, $email) {
        $stmt = $this->db->prepare('SELECT `account`, `email` FROM `users` WHERE REPLACE(`account`, ".", "") = :account OR `email` = :email LIMIT 1');
        $stmt->execute(['account' => str_replace('.', '', $account), 'email' => $email]);
        $data = $stmt->fetch();
        return $data;
    }

    // register
    public function register($account, $password, $email)
    {
        $stmt = $this->db->prepare('INSERT INTO `users` SET
            `account`      = :account,
            `password`     = :password,
            `email`        = :email,
            `join_date`    = :join_date,
            `last_login`   = :last_login
        ');
        $stmt->execute([
            'account'      => $account,
            'password'     => md5(md5($password)),
            'email'        => $email,
            'join_date'    => TIME,
            'last_login'   => TIME
        ]);
        return $this->db->lastInsertId();
    }
}
