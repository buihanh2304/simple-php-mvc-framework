<?php

declare(strict_types=1);

namespace Tests\Unit;

use Exception;
use InvalidArgumentException;
use stdClass;
use System\Classes\Container;
use Tests\Fixtures\AbstractThing;
use Tests\Fixtures\ClassWithDefault;
use Tests\Fixtures\Greeter;
use Tests\Fixtures\InvokableHandler;
use Tests\Fixtures\SimpleService;
use Tests\Fixtures\Unresolvable;
use Tests\TestCase;

class ContainerTest extends TestCase
{
    public function testGetInstanceAlwaysReturnsTheSameContainer(): void
    {
        $this->assertSame(Container::getInstance(), Container::getInstance());
    }

    public function testInstanceStoresAndReturnsTheGivenObject(): void
    {
        $object = new stdClass();
        $returned = $this->container()->instance(stdClass::class, $object);

        $this->assertSame($object, $returned);
        $this->assertSame($object, $this->container()->make(stdClass::class));
    }

    public function testBindWithoutSharedCreatesNewInstances(): void
    {
        $this->container()->bind(stdClass::class);

        $first = $this->container()->make(stdClass::class);
        $second = $this->container()->make(stdClass::class);

        $this->assertInstanceOf(stdClass::class, $first);
        $this->assertNotSame($first, $second);
    }

    public function testSingletonReturnsTheSameInstance(): void
    {
        $this->container()->singleton(stdClass::class);

        $first = $this->container()->make(stdClass::class);
        $second = $this->container()->make(stdClass::class);

        $this->assertSame($first, $second);
    }

    public function testBindAliasResolvesConcreteClass(): void
    {
        $this->container()->bind('greeter', Greeter::class);

        $greeter = $this->container()->make('greeter');

        $this->assertInstanceOf(Greeter::class, $greeter);
        $this->assertInstanceOf(SimpleService::class, $greeter->service);
    }

    public function testBindClosureReceivesContainerAndParameters(): void
    {
        $this->container()->bind('greeting', static function (Container $container, array $parameters) {
            return 'hello-' . ($parameters['name'] ?? 'anon');
        });

        $this->assertSame('hello-ada', $this->container()->make('greeting', ['name' => 'ada']));
    }

    public function testMakeBuildsConstructorDependencies(): void
    {
        $greeter = $this->container()->make(Greeter::class);

        $this->assertSame('pong world', $greeter->greet());
    }

    public function testMakeUsesDefaultConstructorValues(): void
    {
        $object = $this->container()->make(ClassWithDefault::class);

        $this->assertSame('default', $object->name);
    }

    public function testMakeThrowsWhenTargetIsNotInstantiable(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('is not instantiable');

        $this->container()->make(AbstractThing::class);
    }

    public function testMakeThrowsWhenDependencyCannotBeResolved(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unresolvable dependency');

        $this->container()->make(Unresolvable::class);
    }

    public function testCallInvokesClosureWithResolvedDependencies(): void
    {
        $result = $this->container()->call(static function (SimpleService $service, string $name) {
            return $service->ping() . '-' . $name;
        }, ['name' => 'ada']);

        $this->assertSame('pong-ada', $result);
    }

    public function testCallInvokesClassMethodWithDefaultParameters(): void
    {
        $result = $this->container()->call([Greeter::class, 'greet']);

        $this->assertSame('pong world', $result);
    }

    public function testCallInvokesNamedMethodWithOverride(): void
    {
        $result = $this->container()->call([Greeter::class, 'greet'], ['name' => 'Ada']);

        $this->assertSame('pong Ada', $result);
    }

    public function testMakeInvokesInvokableClassInstances(): void
    {
        $this->assertSame('ok', $this->container()->make(InvokableHandler::class));
    }

    public function testCallThrowsWhenMethodIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method not provided.');

        $this->container()->call([SimpleService::class]);
    }

    public function testBindClearsPreviousInstance(): void
    {
        $first = new stdClass();
        $this->container()->instance(stdClass::class, $first);
        $this->container()->bind(stdClass::class);

        $this->assertNotSame($first, $this->container()->make(stdClass::class));
    }
}
