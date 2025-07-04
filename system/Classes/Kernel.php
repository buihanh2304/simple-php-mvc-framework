<?php
declare(strict_types=1);

namespace System\Classes;

use GdImage;

class Kernel
{
    public function run(Request $request): void
    {
        $this->removeHeaders();
        /** @var Router */
        $router = app(Router::class);
        $router->setAllowedMethods($request->getAllowedMethods());
        $router->setBasePath(SITE_PATH);
        $this->matchRoute($request, $router);
        die('ERROR: 404 Not Found!');
    }

    protected function matchRoute(Request $request, Router $router): void
    {
        $router->match($request->getMethod(), $request->getRoute());
        $callable = $router->getRequestParams();
        if ($callable) {
            $callback = $callable['callback'];
            if (is_array($callback)) {
                $result = app()->call([
                    $callback['controller'],
                    $callback['method'],
                ], $callable['params']);
            } else {
                $result = app()->call($callback, $callable['params']);
            }
            if ($result) {
                if (is_array($result)) {
                    header('Content-Type: application/json');
                    echo json_encode($result);
                } elseif ($result instanceof GdImage) {
                    header('Content-Type: image/png');
                    imagepng($result);
                    imagedestroy($result);
                } else {
                    echo $result;
                }
                exit;
            }
        }
        /** @var \System\Classes\Template */
        $view = view();
        echo $view->render('404');
    }

    protected function removeHeaders(): void
    {
        if (function_exists('header_remove')) {
            header_remove();
        }
    }
}
