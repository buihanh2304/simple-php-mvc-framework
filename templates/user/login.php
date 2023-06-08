<?php
$this->layout('user/container');
?>
<div class="row">
    <div class="col-12 col-md-6 col-lg-4 m-auto">
        <div class="card">
            <div class="card-header">Đăng nhập</div>
            <div class="card-body">
<?php if ($error): ?>
                <div class="alert alert-warning mb-3"><?=$error?></div>
<?php endif ?>
                <form action="<?=$site_path?>/login" method="post" class="mb-2">
                    <div class="form-group">
                        <input type="text" name="email" class="form-control" id="inputEmail" placeholder="Email hoặc tên tài khoản" value="<?=$inputEmail?>" />
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Mật khẩu" />
                    </div>
                    <div class="row my-2">
                        <div class="col-12">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="remember" value="1"<?php echo ($inputRemember ? ' checked="checked"' : ''); ?> /> Ghi nhớ
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Đăng nhập</button>
                </form>
                <p class="text-center"><a href="<?=$site_path?>/register">Chưa có tài khoản? Đăng ký</a></p>
                <p class="text-center mb-0"><a href="<?=$site_path?>/">Trở lại</a></p>
            </div>
        </div>
    </div>
</div>