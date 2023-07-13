<?php

namespace App\Providers;

use System\Classes\Container;
use System\Classes\Router;
use System\Interfaces\ServiceProviderInterface;

class RouteServiceProvider implements ServiceProviderInterface
{
    protected $namespace = 'App\\Controllers\\';

    public function register()
    {
        $router = Container::get(Router::class);
        $router->setNamespace($this->namespace);

        $this->loadRoutes();
    }

    protected function loadRoutes()
    {
        require_once ROOT . 'app' . DS . 'routes.php';
    }
}
