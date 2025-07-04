<?php
declare(strict_types=1);

namespace System\Classes;

use Medoo\Medoo;

class Auth
{
    public int $id = 0;
    public int $rights = 0;
    public bool $isLogin = false;
    public array $user = [
        'id' => 0,
        'account' => '',
        'password' => '',
        'email' => '',
        'join_date' => '',
        'rights' => 0,
        'last_login' => 0,
        'name' => '',
    ];
    public mixed $settings;

    public function __construct(protected Medoo $db)
    {
        $this->authorize();
    }

    private function authorize(): void
    {
        $id = 0;
        $password = '';
        if (isset($_SESSION['uid']) && isset($_SESSION['ups'])) {
            $id = intval(trim((string)$_SESSION['uid']));
            $password = trim($_SESSION['ups']);
        } elseif (isset($_COOKIE['cuid']) && isset($_COOKIE['cups'])) {
            $id = intval(base64_decode(trim($_COOKIE['cuid'])));
            $password = md5(trim($_COOKIE['cups']));
            $_SESSION['uid'] = $id;
            $_SESSION['ups'] = $password;
        }
        if ($id && $password) {
            $user = $this->db->get('users', '*', ['id' => $id]);
            if ($user) {
                if ($password === $user['password']) {
                    $this->isLogin = true;
                    $this->id = (int) $user['id'];
                    $this->rights = (int) $user['rights'];
                    $this->user = $user;
                    $this->db->update('users', ['last_login' => TIME], ['id' => $user['id']]);
                } else {
                    $this->unset();
                }
            } else {
                $this->unset();
            }
        }
    }

    private function unset(): void
    {
        unset($_SESSION['uid']);
        unset($_SESSION['ups']);
        setcookie('cuid', '', TIME - 60, COOKIE_PATH);
        setcookie('cups', '', TIME - 60, COOKIE_PATH);
    }
}
