<?php

declare(strict_types=1);

namespace Tests\Support;

use System\Classes\Kernel;
use System\Classes\Request;
use System\Classes\Router;

class TestableKernel extends Kernel
{
    public function exposeRemoveHeaders(): void
    {
        $this->removeHeaders();
    }

    public function exposeMatchRoute(Request $request, Router $router): void
    {
        $this->matchRoute($request, $router);
    }
}
