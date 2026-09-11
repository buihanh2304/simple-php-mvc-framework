<?php

declare(strict_types=1);

namespace Tests\Unit;

use System\Classes\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testGuestWhenSessionAndCookiesAreEmpty(): void
    {
        $auth = new Auth($this->mockPdo());

        $this->assertFalse($auth->isLogin);
        $this->assertSame(0, $auth->id);
        $this->assertSame(0, $auth->rights);
        $this->assertSame('', $auth->user['account']);
    }

    public function testLogsInFromMatchingSessionCredentials(): void
    {
        $user = $this->sampleUser();
        $_SESSION['uid'] = $user['id'];
        $_SESSION['ups'] = $user['password'];

        $auth = new Auth($this->mockPdo($user));

        $this->assertTrue($auth->isLogin);
        $this->assertSame(1, $auth->id);
        $this->assertSame(0, $auth->rights);
        $this->assertSame('admin', $auth->user['account']);
    }

    public function testLogsInFromRememberCookies(): void
    {
        $user = $this->sampleUser(['password' => md5('cookie-secret')]);
        $_COOKIE['cuid'] = base64_encode((string) $user['id']);
        $_COOKIE['cups'] = 'cookie-secret';

        $auth = new Auth($this->mockPdo($user));

        $this->assertTrue($auth->isLogin);
        $this->assertSame(1, $_SESSION['uid']);
        $this->assertSame(md5('cookie-secret'), $_SESSION['ups']);
    }

    public function testClearsSessionWhenPasswordDoesNotMatch(): void
    {
        $user = $this->sampleUser();
        $_SESSION['uid'] = $user['id'];
        $_SESSION['ups'] = 'not-the-hash';

        $this->withoutHeaderWarnings(function () use ($user) {
            $auth = new Auth($this->mockPdo($user));
            $this->assertFalse($auth->isLogin);
        });

        $this->assertArrayNotHasKey('uid', $_SESSION);
        $this->assertArrayNotHasKey('ups', $_SESSION);
    }

    public function testClearsSessionWhenUserIsMissing(): void
    {
        $_SESSION['uid'] = 99;
        $_SESSION['ups'] = 'hash';

        $this->withoutHeaderWarnings(function () {
            $auth = new Auth($this->mockPdo());
            $this->assertFalse($auth->isLogin);
        });

        $this->assertArrayNotHasKey('uid', $_SESSION);
    }
}
