<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class ClassWithDefault
{
    public function __construct(public string $name = 'default')
    {
    }
}
