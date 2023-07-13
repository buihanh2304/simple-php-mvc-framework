<?php

namespace App\Providers;

use System\Classes\Router;
use System\Interfaces\ServiceProviderInterface;

class RouteServiceProvider implements ServiceProviderInterface
{
    protected $namespace = 'App\\Controllers\\';

    public function __construct(
        protected Router $router
    ) {
        //
    }

    public function register()
    {
        $this->router->setNamespace($this->namespace);

        $this->loadRoutes();
    }

    protected function loadRoutes()
    {
        $router = $this->router;

        (function () use ($router) {
            require_once ROOT . 'app' . DS . 'routes.php';
        })();
    }
}
