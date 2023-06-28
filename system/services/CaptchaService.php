<?php

class CaptchaService implements ServiceInterface
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
