<?php

declare(strict_types=1);

namespace Tests\Unit;

use System\Classes\Auth;
use Tests\TestCase;

class RequestTest extends TestCase
{
    public function testGetVarReturnsTypedGetValues(): void
    {
        $_GET['page'] = '12';
        $_GET['flag'] = '1';
        $_GET['tags'] = ['a', 'b'];
        $_GET['q'] = "  hello\x00world  ";

        $request = $this->makeRequest();

        $this->assertSame(12, $request->getVar('page', 0));
        $this->assertTrue($request->getVar('flag', false));
        $this->assertSame(['a', 'b'], $request->getVar('tags', []));
        $this->assertSame('helloworld', $request->getVar('q', ''));
        $this->assertSame(5, $request->getVar('missing', 5));
        $this->assertTrue($request->issetGet('page'));
        $this->assertFalse($request->issetGet('missing'));
    }

    public function testPostVarTruncatesStringWhenRequested(): void
    {
        $_POST['name'] = str_repeat('a', 300);
        $_POST['count'] = '9';

        $request = $this->makeRequest(['REQUEST_METHOD' => 'POST']);

        $this->assertSame(255, mb_strlen($request->postVar('name', '', 1)));
        $this->assertSame(9, $request->postVar('count', 0));
        $this->assertTrue($request->issetPost('name'));
        $this->assertFalse($request->issetPost('missing'));
        $this->assertTrue($request->isPost());
        $this->assertTrue($request->checkMethod('post'));
    }

    public function testDetectsAjaxFromHeader(): void
    {
        $request = $this->makeRequest([
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]);

        $this->assertTrue($request->isAjax());
        $this->assertSame('GET', $request->getMethod());
        $this->assertFalse($request->isPost());
    }

    public function testOverridesPostMethodFromXMethodHeader(): void
    {
        $request = $this->makeRequest([
            'REQUEST_METHOD' => 'POST',
            'HTTP_X_METHOD' => 'PUT',
        ]);

        $this->assertSame('PUT', $request->getMethod());
        $this->assertFalse($request->isPost());
        $this->assertTrue($request->checkMethod('PUT'));
    }

    public function testGetRouteStripsQueryStringAndSlashes(): void
    {
        $request = $this->makeRequest([
            'REQUEST_URI' => '/users/profile/?tab=info',
        ]);

        $this->assertSame('users/profile', $request->getRoute());
    }

    public function testGetRouteKeepsRootPath(): void
    {
        $request = $this->makeRequest([
            'REQUEST_URI' => '/',
        ]);

        $this->assertSame('/', $request->getRoute());
    }

    public function testProcessUserAgentPrefersOperaMiniHeader(): void
    {
        $request = $this->makeRequest([
            'HTTP_X_OPERAMINI_PHONE_UA' => 'OperaMiniDevice',
            'HTTP_USER_AGENT' => 'Mozilla',
        ]);

        $this->assertSame('Opera Mini: OperaMiniDevice', $request->getUserAgent());
    }

    public function testProcessUserAgentFallsBackToNotRecognised(): void
    {
        $request = $this->makeRequest();

        $this->assertSame('Not Recognised', $request->getUserAgent());
    }

    public function testGetIpReadsRemoteAddress(): void
    {
        $this->makeRequest(['REMOTE_ADDR' => '8.8.8.8']);
        $request = $this->makeRequest(['REMOTE_ADDR' => '8.8.8.8']);

        $this->assertSame('8.8.8.8', $request->getIp());
        $this->assertSame(0, $request->getIpViaProxy());
        $this->assertSame(['8.8.8.8'], $request->getIpList());
    }

    public function testFloodTrackingDoesNotCollapseDifferentAddresses(): void
    {
        $this->makeRequest(['REMOTE_ADDR' => '8.8.8.8']);
        $request = $this->makeRequest(['REMOTE_ADDR' => '1.2.3.4']);

        $this->assertSame(['8.8.8.8'], $request->getIpList());
        $this->assertNotContains('1.2.3.4', $request->getIpList());
    }

    public function testGetIpAcceptsIpv6RemoteAddress(): void
    {
        $request = $this->makeRequest(['REMOTE_ADDR' => '2001:4860:4860::8888']);

        $this->assertSame('2001:4860:4860::8888', $request->getIp());
    }

    public function testGetIpCanonicalizesExpandedIpv6(): void
    {
        $request = $this->makeRequest([
            'REMOTE_ADDR' => '2001:0db8:0000:0000:0000:0000:0000:0001',
        ]);

        $this->assertSame('2001:db8::1', $request->getIp());
    }

    public function testGetIpMapsIpv4MappedIpv6ToIpv4(): void
    {
        $request = $this->makeRequest(['REMOTE_ADDR' => '::ffff:8.8.8.8']);

        $this->assertSame('8.8.8.8', $request->getIp());
    }

    public function testFloodTrackingTreatsCompressedAndExpandedIpv6AsTheSameClient(): void
    {
        $this->makeRequest(['REMOTE_ADDR' => '2001:0db8:0000:0000:0000:0000:0000:0001']);
        $request = $this->makeRequest(['REMOTE_ADDR' => '2001:db8::1']);

        $this->assertSame(['2001:db8::1'], $request->getIpList());
    }

    public function testFloodTrackingDoesNotCollapseDifferentIpv6Addresses(): void
    {
        $this->makeRequest(['REMOTE_ADDR' => '2001:4860:4860::8888']);
        $request = $this->makeRequest(['REMOTE_ADDR' => '2606:4700:4700::1111']);

        $this->assertSame(['2001:4860:4860::8888'], $request->getIpList());
        $this->assertNotContains('2606:4700:4700::1111', $request->getIpList());
    }

    public function testProcessIpViaProxyIgnoresPrivateAddresses(): void
    {
        $request = $this->makeRequest([
            'REMOTE_ADDR' => '8.8.8.8',
            'HTTP_X_FORWARDED_FOR' => '10.0.0.1, 172.17.0.1, 1.2.3.4',
        ]);

        $this->assertSame('1.2.3.4', $request->getIpViaProxy());
    }

    public function testProcessIpViaProxyReadsIpv6FromForwardedHeader(): void
    {
        $request = $this->makeRequest([
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_FOR' => '2001:4860:4860::8888',
        ]);

        $this->assertSame('2001:4860:4860::8888', $request->getIpViaProxy());
    }

    public function testProcessIpViaProxyReadsBracketedIpv6(): void
    {
        $request = $this->makeRequest([
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_FOR' => '[2001:4860:4860::8844]:443',
        ]);

        $this->assertSame('2001:4860:4860::8844', $request->getIpViaProxy());
    }

    public function testProcessIpViaProxySkipsPrivateAndReservedIpv6(): void
    {
        $request = $this->makeRequest([
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_FOR' => 'fc00::1, fe80::1, ::1, 2606:4700:4700::1111',
        ]);

        $this->assertSame('2606:4700:4700::1111', $request->getIpViaProxy());
    }

    public function testProcessIpViaProxyHandlesMixedIpv4AndIpv6(): void
    {
        $request = $this->makeRequest([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => '10.0.0.1, 2001:4860:4860::8888, 1.2.3.4',
        ]);

        $this->assertSame('2001:4860:4860::8888', $request->getIpViaProxy());
    }

    public function testAllowedMethodsIncludeCommonHttpVerbs(): void
    {
        $request = $this->makeRequest();

        $this->assertSame(['POST', 'GET', 'DELETE', 'PUT', 'HEAD'], $request->getAllowedMethods());
    }

    public function testUserReturnsAuthFromContainer(): void
    {
        $this->mockPdo();
        $auth = new Auth($this->container()->make(\PDO::class));
        $this->container()->instance(Auth::class, $auth);

        $request = $this->makeRequest();

        $this->assertSame($auth, $request->user());
        $this->assertFalse($request->user()->isLogin);
    }

    public function testGetVarRejectsNonArrayWhenDefaultIsArray(): void
    {
        $_GET['tags'] = 'not-an-array';
        $request = $this->makeRequest();

        $this->assertSame(['fallback'], $request->getVar('tags', ['fallback']));
    }
}
