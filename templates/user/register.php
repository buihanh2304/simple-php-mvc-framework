<?php
$this->layout('user/container');
?>
<div class="row">
    <div class="col-12 col-md-6 col-lg-4 m-auto">
        <div class="card">
            <div class="card-header">Đăng ký</div>
            <div class="card-body">
<?php       if ($error): ?>
                <div class="alert alert-warning"><?=$error?></div>
<?php       endif ?>
                <form action="<?= url('/register') ?>" method="post" class="mb-2">
                    <div class="form-group">
                        <input type="text" name="account" class="form-control" id="inputAccount" placeholder="Tên tài khoản" value="<?=$inputAccount?>" />
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Mật khẩu" />
                    </div>
                    <div class="form-group">
                        <input type="password" name="re_password" class="form-control" id="inputRePassword" placeholder="Nhập lại mật khẩu" />
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Email" value="<?=$inputEmail?>" />
                    </div>
                    <div class="form-group">
                        <div class="row mb-0">
                            <div class="col-6">
                                <input type="text" name="captcha" class="form-control" id="inputcaptcha" placeholder="Mã bảo vệ" />
                            </div>
                            <div class="col-6">
                                <img src="<?= captchaSrc() ?>" alt="Captcha" />
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Đăng ký</button>
                </form>
                <p class="text-center"><a href="<?= url('/login') ?>">Đã có có tài khoản? Đăng nhập</a></p>
                <p class="text-center mb-0"><a href="<?= url('/') ?>">Trở lại</a></p>
            </div>
        </div>
    </div>
</div>
