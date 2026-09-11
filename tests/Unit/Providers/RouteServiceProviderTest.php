<?php

declare(strict_types=1);

namespace Tests\Unit\Providers;

use ReflectionClass;
use System\Classes\Router;
use System\Providers\RouteServiceProvider;
use Tests\TestCase;

class RouteServiceProviderTest extends TestCase
{
    public function testRegisterSetsNamespaceAndLoadsTheRoutesFile(): void
    {
        $router = new Router();
        (new RouteServiceProvider($router))->register();

        $router->add('probe', 'ProbeController@show');
        $router->match('GET', 'probe');

        $this->assertSame(
            'App\\Controllers\\ProbeController',
            $router->getRequestParams()['callback']['controller']
        );

        $routes = (new ReflectionClass(Router::class))->getProperty('routes')->getValue($router);

        $this->assertNotEmpty($routes['GET']);
    }
}
