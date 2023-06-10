<?php

class Kernel
{
    public function run(Request $request)
    {
        /** @var Config */
        $config = Core::get('Config');
        /** @var Router */
        $router = Core::get('Router');

        $router->set_allowed_methods($request->get_allowed_methods());
        $router->set_base_path(SITE_PATH);

        foreach ($config->routes() as $route) {
            $router->add(...$route);
        }

        $this->matchRoute($request, $router);

        die('ERROR: 404 Not Found!');
    }

    protected function matchRoute(Request $request, Router $router)
    {
        $router->match($request->get_method(), $request->get_route());

        $requestParams = $router->get_request_params();

        if ($requestParams) {
            $controllerObj = Core::get('Loader')->controller($requestParams['controller']);
            if ($controllerObj) {
                if (method_exists($controllerObj, $requestParams['method'])) {
                    call_user_func_array(array($controllerObj, $requestParams['method']), $requestParams['params']); exit;
                }
            }
        }

        if (defined('DEFAULT_CONTROLLER')) {
            $controllerObj = Core::get('Loader')->controller(DEFAULT_CONTROLLER);
            if ($controllerObj) {
                if (method_exists($controllerObj, 'error_404')) {
                    call_user_func_array(array($controllerObj, 'error_404'), array()); exit;
                }
            }
        }
    }
}
