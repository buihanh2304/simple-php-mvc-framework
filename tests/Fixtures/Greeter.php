<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class Greeter
{
    public function __construct(public SimpleService $service)
    {
    }

    public function greet(string $name = 'world'): string
    {
        return $this->service->ping() . ' ' . $name;
    }
}
