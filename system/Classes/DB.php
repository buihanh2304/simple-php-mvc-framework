<?php
declare(strict_types=1);

namespace System\Classes;

use Medoo\Medoo;

class DB
{
    public function __invoke(): Medoo
    {
        $db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $db_user = defined('DB_USER') ? DB_USER : '';
        $db_pass = defined('DB_PASS') ? DB_PASS : '';
        $db_name = defined('DB_NAME') ? DB_NAME : '';

        $database = new Medoo([
            'type' => 'mysql',
            'host' => $db_host,
            'database' => $db_name,
            'username' => $db_user,
            'password' => $db_pass
        ]);

        return $database;
    }
}
