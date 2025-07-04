<?php
declare(strict_types=1);

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

namespace App\Models;

use System\Classes\Model;

class User extends Model
{
    public function logout(): void
    {
        setcookie('cuid', '', TIME - 60, COOKIE_PATH);
        setcookie('cups', '', TIME - 60, COOKIE_PATH);
        unset($_SESSION['uid']);
        unset($_SESSION['ups']);
    }

    // check if user exists for login
    public function getForLogin(string $type, string $email): array|false
    {
        return $this->db->get('users', '*', [
            $type => $email
        ]);
    }

    // check if account or email is existing for register
    public function checkUsedInfo(string $account, string $email): array|false
    {
        // Check by email first
        $user = $this->db->get('users', ['account', 'email'], ['email' => $email]);
        if ($user) {
            return $user;
        }
        // Check by account (ignoring dots)
        $users = $this->db->select('users', ['account', 'email']);
        foreach ($users as $u) {
            if (str_replace('.', '', $u['account']) === str_replace('.', '', $account)) {
                return $u;
            }
        }
        return false;
    }

    // register
    public function register(string $account, string $password, string $email): bool|string
    {
        $this->db->insert('users', [
            'account'      => $account,
            'password'     => md5(md5($password)),
            'email'        => $email,
            'join_date'    => TIME,
            'last_login'   => TIME
        ]);

        return $this->db->id();
    }
}
