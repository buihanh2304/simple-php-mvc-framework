<!DOCTYPE html>
<html lang="vi-VN">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
        <title><?= (isset($page_title) ? $page_title . ' | ' : '') . config('system.app.name'); ?></title>
        <link href="<?=$site_path?>/css/bootstrap.min.css" rel="stylesheet" />
        <link href="<?=$site_path?>/css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <?=$this->section('content')?>
    </body>
</html>
