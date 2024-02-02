<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

namespace System\Classes;

class Captcha
{
    private $font = 'monofont.ttf';
    private $width = 96;
    private $height = 40;
    private $length = 4;

    protected function generateCode()
    {
        /* list all possible characters, similar looking characters and vowels have been removed */
        $possible = '23456789abcdeghkmnpqsuvxyz';
        $code = '';
        $i = 0;
        $max_range = strlen($possible) - 1;
        while ($i < $this->length) {
            $code .= substr($possible, mt_rand(0, $max_range), 1);
            $i++;
        }
        return $code;
    }

    public function check($name = 'captcha')
    {
        $request = app(Request::class);
        $code = isset($_SESSION['code']) ? trim($_SESSION['code']) : '';
        $captcha = $request->postVar($name, '');

        if ($code && $captcha && mb_strlen($captcha) == $this->length && $captcha === $code) {
            unset($_SESSION['code']);

            return true;
        }

        return false;
    }

    public function generateImage()
    {
        $font = SYSTEM . 'files' . DS . 'fonts' . DS . $this->font;
        $code = $this->generateCode();
        /* font size will be 75% of the image height */
        $font_size = round($this->height * 0.75);
        $image = imagecreate($this->width, $this->height);
        /* set the colours */
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 20, 40, 100);
        $noise_color = imagecolorallocate($image, 100, 120, 180);

        /* generate random dots in background */
        for ($i = 0, $j = round(($this->width * $this->height) / 3); $i < $j; $i++) {
            imagefilledellipse($image, mt_rand(0, $this->width), mt_rand(0, $this->height), 1, 1, $noise_color);
        }

        /* generate random lines in background */
        for ($i = 0, $j = round(($this->width * $this->height) / 150); $i < $j; $i++) {
            imageline($image, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $noise_color);
        }

        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $font, $code);
        $x = round(($this->width - $textbox[4]) / 2);
        $y = round(($this->height - $textbox[5]) / 2);
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $code);
        /* output captcha image */
        $_SESSION['code'] = $code;

        return $image;
    }
}
