<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

class UserController extends Controller
{
    private UserModel $userModel;
    private UserLibrary $userLibrary;

    function __construct()
    {
        parent::__construct();
        $this->userModel = $this->load->model('User');
        $this->userLibrary = $this->load->library('User');
    }

    public function logout()
    {
        $this->userModel->logout();
        redirect('/');
    }

    public function login()
    {
        if ($this->auth->isLogin) {
            redirect('/');
        }

        $error = false;
        $email = $this->request->postVar('email', '');
        $password = $this->request->postVar('password', '');
        $remember = $this->request->postVar('remember', 0);

        if ($this->request->getMethod() === 'POST') {
            if (empty($email) || empty($password)) {
                $error = 'Vui lòng nhập tên tài khoản và mật khẩu';
            } else {
                $type = (mb_strpos($email, '@') !== false) ? 'email' : 'account';

                switch ($type) {
                    case 'email':
                        $error = $this->userLibrary->validateEmail($email);
                        break;

                    default:
                        $error = $this->userLibrary->validateAccount($email);
                }

                if (!$error) {
                    $error = $this->userLibrary->validatePassword($password);
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

        $this->view->setTitle('Đăng nhập');

        return $this->view->render('user/login', [
            'error'         => $error,
            'inputEmail'    => _e($email),
            'inputRemember' => $remember
        ]);
    }

    public function register()
    {
        if ($this->auth->isLogin) {
            redirect('/');
        }

        $error = [];
        $captcha = Container::get(Captcha::class);
        $account = $this->request->postVar('account', '');
        $password = $this->request->postVar('password', '');
        $re_password = $this->request->postVar('re_password', '');
        $email = $this->request->postVar('email', '');

        if ($this->request->getMethod() === 'POST') {
            // check account
            $check = $this->userLibrary->validateAccount($account);

            if ($check) {
                $error[] = $check;
            }

            // check password
            $check = $this->userLibrary->validatePassword($password);

            if ($check) {
                $error[] = $check;
            }

            // check repeat password
            $check = $this->userLibrary->validatePasswordConfirmation($password, $re_password);

            if ($check) {
                $error[] = $check;
            }

            // check email
            $check = $this->userLibrary->validateEmail($email);

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

        $this->view->setTitle('Đăng ký');

        return $this->view->render('user/register', [
            'error'        => $error,
            'inputAccount' => _e($account),
            'inputEmail'   => _e($email),
        ]);

    }
}
