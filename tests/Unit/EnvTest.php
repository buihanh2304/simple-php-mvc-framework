<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use System\Classes\Env;
use Tests\TestCase;

class EnvTest extends TestCase
{
    #[DataProvider('booleanAndSpecialValues')]
    public function testGetCastsSpecialStringValues(string $raw, mixed $expected): void
    {
        putenv('K_MVC_ENV_CAST=' . $raw);

        $this->assertSame($expected, Env::get('K_MVC_ENV_CAST'));

        putenv('K_MVC_ENV_CAST');
    }

    public static function booleanAndSpecialValues(): array
    {
        return [
            'true' => ['true', true],
            'true in parentheses' => ['(true)', true],
            'false' => ['false', false],
            'false in parentheses' => ['(false)', false],
            'empty' => ['empty', ''],
            'empty in parentheses' => ['(empty)', ''],
            'null' => ['null', null],
            'null in parentheses' => ['(null)', null],
        ];
    }

    public function testGetStripsMatchingQuotes(): void
    {
        putenv('K_MVC_ENV_QUOTED="quoted value"');

        $this->assertSame('quoted value', Env::get('K_MVC_ENV_QUOTED'));

        putenv('K_MVC_ENV_QUOTED');
    }

    public function testGetReturnsRawStringWhenNotSpecial(): void
    {
        putenv('K_MVC_ENV_PLAIN=hello-world');

        $this->assertSame('hello-world', Env::get('K_MVC_ENV_PLAIN'));

        putenv('K_MVC_ENV_PLAIN');
    }

    public function testGetUsesDefaultWhenKeyIsMissing(): void
    {
        $this->assertSame('fallback', Env::get('K_MVC_ENV_DOES_NOT_EXIST', 'fallback'));
        $this->assertSame('from-closure', Env::get('K_MVC_ENV_DOES_NOT_EXIST', static fn () => 'from-closure'));
    }

    public function testGetRepositoryIsReused(): void
    {
        $this->assertSame(Env::getRepository(), Env::getRepository());
    }
}
