<?php

declare(strict_types=1);

namespace Tests\Unit\Providers;

use System\Classes\Captcha;
use System\Classes\Router;
use System\Providers\CaptchaServiceProvider;
use Tests\TestCase;

class CaptchaServiceProviderTest extends TestCase
{
    public function testRegisterAddsCaptchaRoute(): void
    {
        $router = new Router();
        $this->container()->instance(Router::class, $router);
        $this->container()->instance(Captcha::class, new Captcha());

        (new CaptchaServiceProvider())->register();

        $router->match('GET', 'captcha');
        $params = $router->getRequestParams();

        $this->assertIsArray($params);
        $this->assertIsCallable($params['callback']);
    }
}
