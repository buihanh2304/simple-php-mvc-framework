<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Support\TestableKernel;
use Tests\TestCase;

class KernelTest extends TestCase
{
    public function testRemoveHeadersCanBeInvokedWithoutError(): void
    {
        (new TestableKernel())->exposeRemoveHeaders();

        $this->addToAssertionCount(1);
    }

    public function testRouterIsConfiguredFromTheIncomingRequest(): void
    {
        $this->bootForViews();
        $request = $this->makeRequest(['REQUEST_URI' => '/missing-page']);
        $router = $this->container()->make(\System\Classes\Router::class);

        $router->setAllowedMethods($request->getAllowedMethods());
        $router->setBasePath(SITE_PATH);
        $router->match($request->getMethod(), $request->getRoute());

        $this->assertFalse($router->getRequestParams());
        $this->assertSame($request->getAllowedMethods(), ['POST', 'GET', 'DELETE', 'PUT', 'HEAD']);
    }
}
