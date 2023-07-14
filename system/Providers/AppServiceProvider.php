<?php

namespace System\Providers;

use PDO;
use System\Classes\Auth;
use System\Classes\Captcha;
use System\Classes\Config;
use System\Classes\Container;
use System\Classes\Controller;
use System\Classes\DB;
use System\Classes\Kernel;
use System\Classes\Model;
use System\Classes\Request;
use System\Classes\Router;
use System\Classes\Template;
use System\Interfaces\ServiceProviderInterface;

class AppServiceProvider implements ServiceProviderInterface
{
    public function __construct(protected Container $container)
    {
        //
    }

    public function register()
    {
        $this->container->bind(Auth::class, null, true);
        $this->container->bind(Captcha::class, null, true);
        $this->container->bind(Config::class, null, true);
        $this->container->bind(Controller::class, null, true);
        $this->container->bind(PDO::class, DB::class, true);
        $this->container->bind(Kernel::class, null, true);
        $this->container->bind(Model::class, null, true);
        $this->container->bind(Request::class, null, true);
        $this->container->bind(Router::class, null, true);
        $this->container->bind(Template::class, null, true);
    }
}
