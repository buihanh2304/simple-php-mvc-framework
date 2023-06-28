<!DOCTYPE html>
<html lang="vi-VN">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
        <title><?= (isset($page_title) ? $page_title . ' | ' : '') . config('system.app.name'); ?></title>
        <link href="<?=$site_path?>/css/bootstrap.min.css" rel="stylesheet" />
        <link href="<?=$this->asset('/css/styles.css') ?>" rel="stylesheet" />
    </head>
    <body class="has-navbar">
        <header>
            <div class="container">
                <div class="d-md-flex flex-row justify-content-between">
                    <div class="text-center py-3" id="logo">
                        <h2 class="mb-0">
                            <a href="<?=$site_path?>/" class="text-success"><?= config('system.app.name') ?></a>
                            <span>MVC Core For Your Projects</span>
                        </h2>
                    </div>
                </div>
            </div>

            <nav class="navbar navbar-expand navbar-dark bg-dark">
                <div class="container">
                    <a class="navbar-brand" href="<?=$site_path?>/"><i class="fa fa-home"></i></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false"><span class="navbar-toggler-icon"></span></button>

                    <div class="collapse navbar-collapse" id="navbar">
                        <ul class="navbar-nav ml-auto">
<?php if ($isLogin): ?>
                            <li class="nav-item">
                                <span class="nav-link">Xin chào <b><?=$user['account']?></b><span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?=$site_path?>/logout">Đăng xuất</a></li>
                            </li>
<?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?=$site_path?>/login">Đăng nhập</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?=$site_path?>/register">Đăng ký</a>
                            </li>
<?php endif ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container">
<?=         $this->section('content')?>
            </div>
        </main>
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-12 copyright">Copyright &copy; 2018-2023 by <a href="https://vdevs.net" target="_blank">vDevs</a> &amp; <a href="https://github.com/buihanh2304" target="_blank">MrKen</a></div>
                </div>
            </div>
        </footer>
    </body>
</html>
