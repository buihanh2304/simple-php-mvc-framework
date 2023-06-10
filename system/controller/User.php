<?php
defined('_MRKEN_MVC') or die('Access denied!!!');

/*
// This file is a part of K-MVC
// version: 0.2
// author: MrKen
// website: https://vdevs.net
*/

class UserController extends Controller
{
    private User $user;
    private UserModel $userModel;

    function __construct()
    {
        parent::__construct();
        $this->userModel = $this->load->model('User');
        $this->user = Core::get('User');
    }

    public function logout()
    {
        $this->userModel->logout();
        redirect('/');
    }

    public function login()
    {
        if ($this->user->isLogin) {
            redirect('/');
        }
        $userLibrary = $this->load->library('User');
        $error = false;
        $email = $this->request->post_var('email', '');
        $password = $this->request->post_var('password', '');
        $remember = $this->request->post_var('remember', 0);
        if ($this->request->get_method() === 'POST') {
            if (empty($email) || empty($password)) {
                $error = 'Vui lòng nhập tên tài khoản và mật khẩu';
            } else {
                $type = (mb_strpos($email, '@') !== false) ? 'email' : 'account';
                switch ($type) {
                    case 'email':
                        $error = $userLibrary->pre_check_email($email);
                        break;

                    default:
                        $error = $userLibrary->pre_check_account($email);
                }
                if (!$error) {
                    $error = $userLibrary->pre_check_password($password);
                }
                if (!$error) {
                    $user = $this->userModel->get_user_login($type, $email);
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

        $view = $this->load->view();
        $view->setTitle('Đăng nhập');
        echo $view->render('user/login', [
            'error'         => $error,
            'inputEmail'    => _e($email),
            'inputRemember' => $remember
        ]);
    }

    public function register()
    {
        if ($this->user->isLogin) {
            redirect('/');
        }
        $error = [];
        $captcha = new Captcha;
        $account = $this->request->post_var('account', '');
        $password = $this->request->post_var('password', '');
        $re_password = $this->request->post_var('re_password', '');
        $email = $this->request->post_var('email', '');
        if ($this->request->get_method() === 'POST') {
            $userLibrary = $this->load->library('User');
            // check account
            $check = $userLibrary->pre_check_account($account);
            if ($check) {
                $error[] = $check;
            }
            // check password
            $check = $userLibrary->pre_check_password($password);
            if ($check) {
                $error[] = $check;
            }
            // check repeat password
            $check = $userLibrary->pre_check_re_password($password, $re_password);
            if ($check) {
                $error[] = $check;
            }
            // check email
            $check = $userLibrary->pre_check_email($email);
            if ($check) {
                $error[] = $check;
            }
            // check captcha
            if ($captcha->check() !== true) {
                $error[] = 'Mã bảo vệ không chính xác';
            }

            if (!$error) {
                $check = $this->userModel->check_used_info($account, $email);
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
        $view = $this->load->view();
        $view->setTitle('Đăng ký');
        echo $view->render('user/register', [
            'error'        => $error,
            'inputAccount' => _e($account),
            'inputEmail'   => _e($email),
            'captchaImage' => $captcha->generateImage()
        ]);

    }
}
