<?php
declare(strict_types=1);

namespace System\Classes;

class Config
{
    private array $configs;

    public function __construct()
    {
        $configs = [];
        foreach (glob(ROOT . 'configs' . DS . 'autoload' . DS . '?*.php') as $file) {
            $configs = array_merge($configs, [
                basename($file, '.php') => include($file)
            ]);
        }
        $this->configs = $configs;
    }

    public function get(?string $key = null, mixed $default = null): mixed
    {
        $result = $this->configs;
        if (is_null($key)) {
            return $result;
        }
        if (isset($result[$key])) {
            return $result[$key];
        }
        $paths = explode('.', (string) $key);
        foreach ($paths as $path) {
            if (isset($result[$path])) {
                $result = $result[$path];
            } else {
                return $default;
            }
        }
        return $result;
    }
}
