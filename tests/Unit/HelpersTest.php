<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function testEscapeEncodesHtmlSpecialCharacters(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(&quot;123&quot;)&lt;/script&gt;',
            _e('<script>alert("123")</script>')
        );
    }

    public function testEscapeTrimsWhitespace(): void
    {
        $this->assertSame('Hello', _e('  Hello  '));
    }

    public function testAppReturnsContainerWhenCalledWithoutArguments(): void
    {
        $this->assertSame($this->container(), app());
    }

    public function testAppResolvesBoundClass(): void
    {
        $this->container()->instance('demo', 'value');

        $this->assertSame('value', app('demo'));
    }

    public function testCaptchaSrcPointsToCaptchaEndpoint(): void
    {
        $src = captchaSrc();

        $this->assertStringStartsWith(SITE_URL . '/captcha?v=', $src);
        $this->assertMatchesRegularExpression('/\?v=\d+$/', $src);
    }

    public function testConfigReturnsConfigInstanceWhenPathIsNull(): void
    {
        $this->assertInstanceOf(\System\Classes\Config::class, config());
    }

    public function testConfigReadsNestedAutoloadValues(): void
    {
        $this->assertIsString(config('system.app.name'));
        $this->assertNotSame('', config('system.app.name'));
    }

    public function testConfigReturnsDefaultForMissingKey(): void
    {
        $this->assertNull(config('missing.key'));
        $this->assertSame('fallback', config('missing.key', 'fallback'));
    }

    public function testDisplayErrorReturnsSingleArrayItem(): void
    {
        $this->assertSame('Only error', display_error(['Only error']));
    }

    public function testDisplayErrorJoinsMultipleItems(): void
    {
        $this->assertSame('- First<br />- Second', display_error(['First', 'Second']));
    }

    public function testDisplayErrorReturnsNonArrayValue(): void
    {
        $this->assertSame('plain', display_error('plain'));
    }

    public function testEnvReadsPutenvValues(): void
    {
        putenv('K_MVC_HELPER_ENV=helper-value');

        $this->assertSame('helper-value', env('K_MVC_HELPER_ENV'));
        $this->assertSame('default', env('K_MVC_HELPER_ENV_MISSING', 'default'));

        putenv('K_MVC_HELPER_ENV');
    }

    public function testPaginationReturnsNothingWhenAllItemsFitOnOnePage(): void
    {
        $page = 3;
        $html = pagination('/page/', $page, 10, 20);

        $this->assertNull($html);
        $this->assertSame(1, $page);
    }

    public function testPaginationClampsPageBelowOne(): void
    {
        $page = 0;
        $html = pagination('/page/', $page, 100, 20);

        $this->assertSame(1, $page);
        $this->assertStringContainsString('page-item active', $html);
        $this->assertStringContainsString('page-item disabled', $html);
        $this->assertStringContainsString('/page/2', $html);
        $this->assertStringContainsString('/page/5', $html);
    }

    public function testPaginationClampsPageAboveMaximum(): void
    {
        $page = 99;
        $html = pagination('/page/', $page, 100, 20);

        $this->assertSame(5, $page);
        $this->assertStringContainsString('>5</span>', $html);
        $this->assertStringContainsString('page-item disabled', $html);
    }

    public function testPaginationRendersNeighborsForMiddlePage(): void
    {
        $page = 5;
        $html = pagination('/items/', $page, 200, 20);

        $this->assertSame(5, $page);
        $this->assertStringContainsString('/items/4', $html);
        $this->assertStringContainsString('/items/6', $html);
        $this->assertStringContainsString('/items/1', $html);
        $this->assertStringContainsString('/items/10', $html);
        $this->assertStringContainsString('page-item active', $html);
    }

    public function testPaginationAppendsSuffix(): void
    {
        $page = 2;
        $html = pagination('/list/', $page, 60, 10, '?sort=name');

        $this->assertStringContainsString('/list/1?sort=name', $html);
        $this->assertStringContainsString('/list/3?sort=name', $html);
    }

    public function testUrlBuildsAbsoluteAndRelativePaths(): void
    {
        $this->assertSame(SITE_URL . '/home', url('/home'));
        $this->assertSame(SITE_URL . '/home', url('home'));
        $this->assertSame('/home', url('/home', false));
        $this->assertSame('/', url('', false));
    }

    public function testValueReturnsScalarAndResolvesClosures(): void
    {
        $this->assertSame(1, value(1));
        $this->assertSame(123, value(static fn () => 123));
        $this->assertSame('ab', value(static fn (string $a, string $b) => $a . $b, 'a', 'b'));
    }

    public function testViewReturnsTemplateInstanceWhenCalledWithoutName(): void
    {
        $this->bootForViews();

        $this->assertInstanceOf(\System\Classes\Template::class, view());
    }

    public function testViewRendersNamedTemplate(): void
    {
        $this->bootForViews();

        $html = view('home/main');

        $this->assertStringContainsString('Xin chào!', $html);
        $this->assertStringContainsString('K-MVC', $html);
    }

    public function testRequestHelperReturnsRequestInstance(): void
    {
        $request = $this->makeRequest();

        $this->assertSame($request, request());
    }
}
