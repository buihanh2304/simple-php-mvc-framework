<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

namespace App\Controllers;

use App\Models\User;
use App\Services\UserService;
use System\Classes\Captcha;
use System\Classes\Controller;
use System\Classes\Request;

class UserController extends Controller
{
    public function __construct(
        protected User $userModel,
        protected UserService $userService
    ) {
        //
    }

    public function logout()
    {
        $this->userModel->logout();
        redirect('/');
    }

    public function login(Request $request)
    {
        if ($request->user()->isLogin) {
            redirect('/');
        }

        $error = false;
        $email = $request->postVar('email', '');
        $password = $request->postVar('password', '');
        $remember = $request->postVar('remember', 0);

        if ($request->getMethod() === 'POST') {
            if (empty($email) || empty($password)) {
                $error = 'Vui lòng nhập tên tài khoản và mật khẩu';
            } else {
                $type = (mb_strpos($email, '@') !== false) ? 'email' : 'account';

                switch ($type) {
                    case 'email':
                        $error = $this->userService->validateEmail($email);
                        break;

                    default:
                        $error = $this->userService->validateAccount($email);
                }

                if (!$error) {
                    $error = $this->userService->validatePassword($password);
                }

                if (!$error) {
                    $user = $this->userModel->getForLogin($type, $email);

                    if ($user && md5(md5($password)) === $user['password']) {
                        $_SESSION['uid'] = $user['id'];
                        $_SESSION['ups'] = $user['password'];

                        if ($remember) {
                            // Save cookie (365 day)
                            setcookie('cuid', base64_encode($user['id']), TIME + 31536000, COOKIE_PATH);
                            setcookie('cups', md5($password), TIME + 31536000, COOKIE_PATH);
                        }
                        redirect('/');
                    } else {
                        $error = ($type === 'email' ? 'Email' : 'Tên tài khoản') . ' hoặc mật khẩu không chính xác';
                    }
                }
            }
        }

        return view()->setTitle('Đăng nhập')
            ->render('user/login', [
                'error'         => $error,
                'inputEmail'    => _e($email),
                'inputRemember' => $remember
            ]);
    }

    public function register(Request $request)
    {
        if ($request->user()->isLogin) {
            redirect('/');
        }

        $error = [];
        $captcha = app(Captcha::class);
        $account = $request->postVar('account', '');
        $password = $request->postVar('password', '');
        $re_password = $request->postVar('re_password', '');
        $email = $request->postVar('email', '');

        if ($request->getMethod() === 'POST') {
            // check account
            $check = $this->userService->validateAccount($account);

            if ($check) {
                $error[] = $check;
            }

            // check password
            $check = $this->userService->validatePassword($password);

            if ($check) {
                $error[] = $check;
            }

            // check repeat password
            $check = $this->userService->validatePasswordConfirmation($password, $re_password);

            if ($check) {
                $error[] = $check;
            }

            // check email
            $check = $this->userService->validateEmail($email);

            if ($check) {
                $error[] = $check;
            }

            // check captcha
            if ($captcha->check() !== true) {
                $error[] = 'Mã bảo vệ không chính xác';
            }

            if (!$error) {
                $check = $this->userModel->checkUsedInfo($account, $email);

                if ($check) {
                    if ($check['email'] === $email) {
                        $error[] = 'Địa chỉ email đã được sử dụng';
                    } else {
                        $error[] = 'Tên tài khoản đã được sử dụng';
                    }
                }
            }

            if (!$error) {
                $user_id = $this->userModel->register($account, $password, $email);

                $_SESSION['uid'] = $user_id;
                $_SESSION['ups'] = md5(md5($password));
                redirect('/');
            } else {
                $error = display_error($error);
            }
        }

        return view()->setTitle('Đăng ký')
            ->render('user/register', [
                'error'        => $error,
                'inputAccount' => _e($account),
                'inputEmail'   => _e($email),
            ]);
    }
}
