<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class Unresolvable
{
    public function __construct(public string $required)
    {
    }
}
