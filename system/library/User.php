<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class UserLibrary
{
    public function pre_check_email($email)
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
        if ($parts[1] !== 'gmail.com') {
            return 'Chỉ chấp nhận Gmail';
        }
        return false;
    }

    public function pre_check_account($account)
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


    public function pre_check_password($password)
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

    public function pre_check_re_password($password, $re_password)
    {
        if ($password !== $re_password) {
            return 'Mật khẩu không trùng khớp';
        }
        return false;

    }

    public function pre_check_name($name)
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
