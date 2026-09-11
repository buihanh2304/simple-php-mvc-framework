<?php

declare(strict_types=1);

namespace Tests\Unit;

use System\Classes\Captcha;
use Tests\TestCase;

class CaptchaTest extends TestCase
{
    public function testCheckFailsWhenSessionCodeIsMissing(): void
    {
        $this->makeRequest(['REQUEST_METHOD' => 'POST']);
        $_POST['captcha'] = 'abcd';

        $this->assertFalse((new Captcha())->check());
    }

    public function testCheckFailsWhenPostedValueDoesNotMatch(): void
    {
        $this->makeRequest(['REQUEST_METHOD' => 'POST']);
        $_SESSION['code'] = 'abcd';
        $_POST['captcha'] = 'wxyz';

        $this->assertFalse((new Captcha())->check());
        $this->assertSame('abcd', $_SESSION['code']);
    }

    public function testCheckFailsWhenLengthDoesNotMatch(): void
    {
        $this->makeRequest(['REQUEST_METHOD' => 'POST']);
        $_SESSION['code'] = 'abcde';
        $_POST['captcha'] = 'abcde';

        $this->assertFalse((new Captcha())->check());
    }

    public function testCheckSucceedsAndClearsSessionCode(): void
    {
        $this->makeRequest(['REQUEST_METHOD' => 'POST']);
        $_SESSION['code'] = 'ab12';
        $_POST['captcha'] = 'ab12';

        $this->assertTrue((new Captcha())->check());
        $this->assertArrayNotHasKey('code', $_SESSION);
    }

    public function testCheckReadsCustomFieldName(): void
    {
        $this->makeRequest(['REQUEST_METHOD' => 'POST']);
        $_SESSION['code'] = 'zz99';
        $_POST['token'] = 'zz99';

        $this->assertTrue((new Captcha())->check('token'));
    }

    public function testGenerateImageCreatesGdImageAndStoresCode(): void
    {
        $font = SYSTEM . 'files' . DS . 'fonts' . DS . 'monofont.ttf';

        if (!is_file($font)) {
            $this->markTestSkipped('Captcha font file is not present in the repository.');
        }

        $image = (new Captcha())->generateImage();

        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertArrayHasKey('code', $_SESSION);
        $this->assertSame(4, strlen($_SESSION['code']));
        imagedestroy($image);
    }
}
