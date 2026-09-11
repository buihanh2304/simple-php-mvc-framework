<?php

declare(strict_types=1);

namespace Tests\Unit;

use System\Classes\DB;
use Tests\TestCase;

class DBTest extends TestCase
{
    public function testInstanceIsInvokable(): void
    {
        $this->assertIsCallable(new DB());
    }
}
