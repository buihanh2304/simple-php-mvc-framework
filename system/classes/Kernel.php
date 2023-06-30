<?php

class Kernel
{
    public function run(Request $request)
    {
        /** @var Router */
        $router = Container::get(Router::class);

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
                $controllerObj = Container::get($callback['controller']);

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
                } else {
                    echo $result;
                }

                exit;
            }
        }
        /** @var Template */
        $view = Container::get(Template::class);

        if ($view->getEngine()->exists('404')) {
            $view->setTitle('404 Not Found');
            header('HTTP/1.1 404 Not Found', true, 404);
            $view->output('404');

            exit;
        }
    }
}
