<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

namespace App\Services;

class UserService
{
    public function validateEmail($email)
    {
        if (empty($email)) {
            return 'Địa chỉ email không được để trống';
        }

        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return 'Địa chỉ email không hợp lệ';
        }

        if (preg_match('/^\.|[^0-9a-z.]|\.\.+|\.$/i', $parts[0])) {
            return 'Địa chỉ email không hợp lệ';
        }

        return false;
    }

    public function validateAccount($account)
    {
        if (empty($account)) {
            return 'Tên tài khoản không được để trống';
        }

        $len = mb_strlen($account);

        if ($len < 5 || $len > 32) {
            return 'Độ dài tên tài khoản phải từ 6 đến 32 ký tự';
        }

        if (preg_match('/[^0-9a-z.]/i', $account)) {
            return 'Tên tài khoản chỉ được sử dụng chữ cái, số và dấu chấm';
        }

        if (preg_match('/^\./', $account)) {
            return 'Tên tài khoản phải bắt đầu bằng chữ cái hoặc số';
        }

        if (preg_match('/\.\.+/', $account)) {
            return 'Tên tài khoản không được chứa hai dấu chấm liên tiếp';
        }

        if (preg_match('/\.$/', $account)) {
            return 'Tên tài khoản phải kết thúc bằng chữ cái hoặc số';
        }

        return false;
    }


    public function validatePassword($password)
    {
        if (empty($password)) {
            return 'Mật khẩu không được để trống';
        }

        $len = mb_strlen($password);

        if ($len < 6 || $len > 32) {
            return 'Độ dài mật khẩu phải từ 6 đến 32 ký tự';
        }

        return false;
    }

    public function validatePasswordConfirmation($password, $passwordConfirmation)
    {
        if ($password !== $passwordConfirmation) {
            return 'Mật khẩu không trùng khớp';
        }

        return false;
    }

    public function validateName($name)
    {
        if (empty($name)) {
            return 'Tên hiển thị không được để trống';
        }

        $len = mb_strlen($name);

        if ($len < 4 || $len > 32) {
            return 'Độ dài tên hiển thị phải từ 5 đến 32 ký tự';
        }

        if (preg_match('/[^[:alnum:]\s]/ui', $name)) {
            return 'Tên hiển thị chỉ có thể sử dụng chữ cái (có dấu), chữ số và khoảng trắng';
        }

        return false;
    }

}
