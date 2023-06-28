<?php

class Kernel
{
    public function run(Request $request)
    {
        /** @var Config */
        $config = Container::get('Config');
        /** @var Router */
        $router = Container::get('Router');

        $router->setAllowedMethods($request->getAllowedMethods());
        $router->setBasePath(SITE_PATH);

        foreach ($config->routes() as $route) {
            $router->add(...$route);
        }

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
                $controllerObj = Container::get('Loader')->controller($callback['controller']);

                if ($controllerObj) {
                    if (method_exists($controllerObj, $callback['method'])) {
                        $result = call_user_func_array([$controllerObj, $callback['method']], $callable['params']);
                    }
                }
            } else {
                $result = call_user_func($callback, ...$callable['params']);
            }

            if ($result) {
                echo $result;
                exit;
            }
        }

        if (defined('DEFAULT_CONTROLLER')) {
            $controllerObj = Container::get('Loader')->controller(DEFAULT_CONTROLLER);
            if ($controllerObj) {
                if (method_exists($controllerObj, 'notFound')) {
                    call_user_func_array([$controllerObj, 'notFound'], []);
                    exit;
                }
            }
        }
    }
}
