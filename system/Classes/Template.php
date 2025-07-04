<?php
declare(strict_types=1);

namespace System\Classes;

use League\Plates\Engine;
use League\Plates\Extension\Asset;

class Template
{
    private Engine $plates;
    private array $global = [];
    private array $data = [];

    public function __construct(Auth $auth, Engine $engine)
    {
        $engine->setDirectory(ROOT . 'templates');
        $engine->loadExtension(new Asset(ROOT . 'public', true));
        $engine->addData([
            'isLogin'            => $auth->isLogin,
            'user'               => $auth->user,
            'rights'             => $auth->rights
        ]);
        $this->plates = $engine;
    }

    public function getEngine(): Engine
    {
        return $this->plates;
    }

    public function setTitle(string $title): static
    {
        $this->addGlobal('page_title', _e($title));
        return $this;
    }

    public function addGlobal(string $name, mixed $value = ''): static
    {
        $data = $this->processData($name, $value);
        $this->global = array_merge($this->global, $data);
        return $this;
    }

    public function addData(string $name, mixed $value = ''): static
    {
        $data = $this->processData($name, $value);
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    private function processData(string $name, mixed $value): array
    {
        $data = [];
        if (is_array($name)) {
            $data = $name;
        } else {
            $data[$name] = $value;
        }
        return $data;
    }

    public function render(string $template, array $data = []): string
    {
        return $this->plates->render($template, array_merge($this->global, $this->data, $data));
    }
}
