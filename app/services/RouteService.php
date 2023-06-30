<?php

class RouteService implements ServiceInterface
{
    public function register()
    {
        $this->loadRoutes();
    }

    protected function loadRoutes()
    {
        require_once ROOT . 'app' . DS . 'routes.php';
    }
}
