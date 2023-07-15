<?php

/*
// This file is a part of K-MVC
// version: 1.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

class DB
{
    public function __invoke()
    {
        $db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $db_user = defined('DB_USER') ? DB_USER : '';
        $db_pass = defined('DB_PASS') ? DB_PASS : '';
        $db_name = defined('DB_NAME') ? DB_NAME : '';

        try {
            $pdo = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8mb4', $db_user, $db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,
                ]
            );
        } catch (PDOException $e) {
            echo '<h2>MySQL ERROR: ' . $e->getCode() . '</h2>';
            switch ($e->getCode()) {
                case 1045:
                    exit('Access credentials (username or password) to a database are incorrect');
                case 1049:
                    exit('The name of a database is specified incorrectly');
                case 2002:
                    exit('Invalid database server');
            }
            exit;
        }

        return $pdo;
    }
}
