<?php

declare(strict_types=1);

namespace Tests;

use League\Plates\Engine;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;
use System\Classes\Auth;
use System\Classes\Config;
use System\Classes\Container;
use System\Classes\DB;
use System\Classes\Request;
use System\Classes\Router;
use System\Classes\Template;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetContainer();
        $this->clearFloodCache();

        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_SESSION = [];

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset(
            $_SERVER['HTTP_X_REQUESTED_WITH'],
            $_SERVER['HTTP_X_METHOD'],
            $_SERVER['HTTP_X_FORWARDED_FOR'],
            $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['REQUEST_URI']
        );
    }

    protected function tearDown(): void
    {
        $this->clearFloodCache();
        parent::tearDown();
    }

    protected function resetContainer(): void
    {
        $reflection = new ReflectionClass(Container::class);
        $property = $reflection->getProperty('instance');
        $property->setValue(null, null);

        $container = Container::getInstance();
        $container->instance(Container::class, $container);
    }

    protected function container(): Container
    {
        return Container::getInstance();
    }

    protected function mockPdo(?array $user = null): PDO
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetch')->willReturn($user ?: false);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($statement);
        $pdo->method('lastInsertId')->willReturn('1');

        $this->container()->instance(PDO::class, $pdo);
        $this->container()->instance(DB::class, $pdo);

        return $pdo;
    }

    protected function bootForViews(?array $user = null): Auth
    {
        $pdo = $this->mockPdo($user);

        if ($user) {
            $_SESSION['uid'] = (int) $user['id'];
            $_SESSION['ups'] = (string) $user['password'];
        }

        $auth = new Auth($pdo);
        $config = new Config();
        $engine = new Engine(ROOT . 'templates');
        $template = new Template($auth, $engine);

        $this->container()->instance(Auth::class, $auth);
        $this->container()->instance(Config::class, $config);
        $this->container()->instance(Template::class, $template);
        $this->container()->instance(Router::class, new Router());

        return $auth;
    }

    protected function makeRequest(array $server = []): Request
    {
        foreach ($server as $key => $value) {
            if ($value === null) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $value;
            }
        }

        $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        set_error_handler(static function (int $severity, string $message): bool {
            if (str_contains($message, 'session_start') || str_contains($message, 'headers already sent')) {
                return true;
            }

            return false;
        });

        try {
            $request = new Request();
        } finally {
            restore_error_handler();
        }

        $this->container()->instance(Request::class, $request);

        return $request;
    }

    protected function sampleUser(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'account' => 'admin',
            'password' => md5(md5('secret1')),
            'email' => 'admin@example.com',
            'join_date' => TIME,
            'rights' => 0,
            'last_login' => TIME,
            'name' => 'Admin',
        ], $overrides);
    }

    protected function withoutHeaderWarnings(callable $callback): mixed
    {
        set_error_handler(static function (int $severity, string $message): bool {
            if (str_contains($message, 'headers already sent') || str_contains($message, 'Cannot modify header')) {
                return true;
            }

            return false;
        });

        try {
            return $callback();
        } finally {
            restore_error_handler();
        }
    }

    private function clearFloodCache(): void
    {
        $file = SYSTEM . 'files' . DS . 'cache' . DS . 'ip.dat';

        if (is_file($file)) {
            @unlink($file);
        }
    }
}
