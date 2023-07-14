<?php

namespace System\Classes;

use GdImage;

class Kernel
{
    public function run(Request $request)
    {
        $this->removeHeaders();

        /** @var Router */
        $router = app(Router::class);

        $router->setAllowedMethods($request->getAllowedMethods());
        $router->setBasePath(SITE_PATH);

        $this->matchRoute($request, $router);

        die('ERROR: 404 Not Found!');
    }

    protected function matchRoute(Request $request, Router $router)
    {
        $router->match($request->getMethod(), $request->getRoute());

        $callable = $router->getRequestParams();

        if ($callable) {
            $callback = $callable['callback'];
            $result = null;

            if (is_array($callback)) {
                $controllerObj = app($callback['controller']);

                if ($controllerObj) {
                    if (method_exists($controllerObj, $callback['method'])) {
                        $result = call_user_func_array([$controllerObj, $callback['method']], $callable['params']);
                    }
                }
            } else {
                $result = call_user_func($callback, ...$callable['params']);
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

        if ($view->getEngine()->exists('404')) {
            $view->setTitle('404 Not Found');
            header('HTTP/1.1 404 Not Found', true, 404);
            $view->output('404');

            exit;
        }
    }

    protected function removeHeaders()
    {
        foreach (headers_list() as $header) {
            if (strpos(strtolower($header), 'x-powered-by:') !== false) {
                header_remove('x-powered-by');
            }
        }
    }
}
