<?php

class CaptchaService implements ServiceInterface
{
    public function register()
    {
        /** @var Router */
        $router = Container::get(Router::class);
        /** @var Captcha */
        $captcha = Container::get(Captcha::class);

        $router->add('captcha', function () use ($captcha) {
            return $captcha->generateImage();
        });
    }
}
