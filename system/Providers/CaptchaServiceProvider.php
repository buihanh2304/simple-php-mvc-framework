<?php

namespace System\Providers;

use System\Classes\Captcha;
use System\Classes\Router;
use System\Interfaces\ServiceProviderInterface;

class CaptchaServiceProvider implements ServiceProviderInterface
{
    public function register()
    {
        /** @var Router */
        $router = app(Router::class);
        /** @var Captcha */
        $captcha = app(Captcha::class);

        $router->add('captcha', function () use ($captcha) {
            return $captcha->generateImage();
        });
    }
}
