<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class SimpleService
{
    public function ping(): string
    {
        return 'pong';
    }
}
