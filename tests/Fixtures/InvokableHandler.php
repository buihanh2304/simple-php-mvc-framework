<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class InvokableHandler
{
    public function __invoke(string $label = 'ok'): string
    {
        return $label;
    }
}
