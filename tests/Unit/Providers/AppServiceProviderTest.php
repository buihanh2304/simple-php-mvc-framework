<?php

declare(strict_types=1);

namespace Tests\Unit\Providers;

use System\Classes\Auth;
use System\Classes\Captcha;
use System\Classes\Config;
use System\Classes\Kernel;
use System\Classes\Router;
use System\Classes\Template;
use System\Providers\AppServiceProvider;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function testRegisterBindsCoreServicesAsSingletons(): void
    {
        (new AppServiceProvider($this->container()))->register();
        $this->mockPdo();

        $router = $this->container()->make(Router::class);
        $config = $this->container()->make(Config::class);
        $auth = $this->container()->make(Auth::class);
        $captcha = $this->container()->make(Captcha::class);
        $kernel = $this->container()->make(Kernel::class);
        $template = $this->container()->make(Template::class);

        $this->assertSame($router, $this->container()->make(Router::class));
        $this->assertSame($config, $this->container()->make(Config::class));
        $this->assertSame($auth, $this->container()->make(Auth::class));
        $this->assertSame($captcha, $this->container()->make(Captcha::class));
        $this->assertSame($kernel, $this->container()->make(Kernel::class));
        $this->assertSame($template, $this->container()->make(Template::class));
        $this->assertFalse($auth->isLogin);
    }
}
