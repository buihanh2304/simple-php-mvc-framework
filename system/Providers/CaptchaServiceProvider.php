<?php

namespace System\Providers;

use System\Classes\Captcha;
use System\Classes\Container;
use System\Classes\Router;
use System\Interfaces\ServiceProviderInterface;

class CaptchaServiceProvider implements ServiceProviderInterface
{
    public function register()
    {
        /** @var Router */
        $router = Container::get(Router::class);
        $captcha = Container::get(Captcha::class);

        $router->add('captcha', function () use ($captcha) {
            header('Content-Type: image/png');

            $captcha->src();
        });
    }
}
