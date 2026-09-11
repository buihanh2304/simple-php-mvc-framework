<?php

declare(strict_types=1);

namespace Tests\Unit;

use System\Classes\Router;
use Tests\Fixtures\Greeter;
use Tests\TestCase;

class RouterTest extends TestCase
{
    public function testMatchReturnsStaticRouteHandler(): void
    {
        $router = new Router();
        $router->add('/', 'PageController@show');
        $router->match('GET', '/');

        $this->assertSame([
            'callback' => [
                'controller' => 'PageController',
                'method' => 'show',
            ],
            'params' => [],
        ], $router->getRequestParams());
    }

    public function testMatchSupportsArrayHandler(): void
    {
        $router = new Router();
        $router->add('/home', [Greeter::class, 'greet']);
        $router->match('GET', 'home');

        $params = $router->getRequestParams();

        $this->assertSame(Greeter::class, $params['callback']['controller']);
        $this->assertSame('greet', $params['callback']['method']);
    }

    public function testMatchSupportsClosureHandler(): void
    {
        $router = new Router();
        $handler = static fn () => 'ok';
        $router->add('/ping', $handler);
        $router->match('GET', 'ping');

        $this->assertSame($handler, $router->getRequestParams()['callback']);
    }

    public function testMatchIsMethodSpecific(): void
    {
        $router = new Router();
        $router->add('/form', 'PageController@submit', 'POST');
        $router->match('GET', '/form');

        $this->assertFalse($router->getRequestParams());
    }

    public function testAddAcceptsPipeSeparatedMethods(): void
    {
        $router = new Router();
        $router->add('/form', 'PageController@submit', 'GET|POST');

        $router->match('POST', 'form');
        $this->assertNotFalse($router->getRequestParams());

        $router->match('GET', 'form');
        $this->assertNotFalse($router->getRequestParams());
    }

    public function testWildcardMethodRegistersAllAllowedMethods(): void
    {
        $router = new Router('', ['GET', 'POST', 'DELETE']);
        $router->add('/item', 'ItemController@handle', '*');

        $router->match('DELETE', 'item');
        $this->assertNotFalse($router->getRequestParams());
    }

    public function testNamespaceIsPrefixedOntoStringHandlers(): void
    {
        $router = new Router();
        $router->setNamespace('Tests\\Fixtures\\');
        $router->add('/', 'PageController@show');
        $router->match('GET', '/');

        $this->assertSame('Tests\\Fixtures\\PageController', $router->getRequestParams()['callback']['controller']);
    }

    public function testBasePathIsStrippedBeforeMatching(): void
    {
        $router = new Router('/app/');
        $router->add('/home', 'PageController@show');
        $router->match('GET', '/app/home');

        $this->assertNotFalse($router->getRequestParams());
    }

    public function testDynamicParameterIsCaptured(): void
    {
        $router = new Router();
        $router->add('posts/{id}', static fn ($id) => $id);
        $router->match('GET', 'posts/10');

        $this->assertSame(['id' => '10'], $router->getRequestParams()['params']);
    }

    public function testIdRuleRejectsLeadingZero(): void
    {
        $router = new Router();
        $router->add('posts/{postId:id}', static fn ($postId) => $postId);
        $router->match('GET', 'posts/10');
        $this->assertSame(['postId' => '10'], $router->getRequestParams()['params']);

        $rejected = new Router();
        $rejected->add('posts/{postId:id}', static fn ($postId) => $postId);
        $rejected->match('GET', 'posts/01');
        $this->assertFalse($rejected->getRequestParams());
    }

    public function testShorthandIdRuleUsesParameterNameId(): void
    {
        $router = new Router();
        $router->add('posts/{:id}', static fn ($id) => $id);
        $router->match('GET', 'posts/7');

        $this->assertSame(['id' => '7'], $router->getRequestParams()['params']);
    }

    public function testNumberRuleAllowsZero(): void
    {
        $router = new Router();
        $router->add('n/{value:number}', static fn ($value) => $value);
        $router->match('GET', 'n/0');

        $this->assertSame(['value' => '0'], $router->getRequestParams()['params']);
    }

    public function testWordRuleRejectsDigits(): void
    {
        $router = new Router();
        $router->add('w/{name:word}', static fn ($name) => $name);
        $router->match('GET', 'w/Ada');
        $this->assertSame(['name' => 'Ada'], $router->getRequestParams()['params']);

        $rejected = new Router();
        $rejected->add('w/{name:word}', static fn ($name) => $name);
        $rejected->match('GET', 'w/Ada2');
        $this->assertFalse($rejected->getRequestParams());
    }

    public function testSlugRuleAcceptsHyphenatedValues(): void
    {
        $router = new Router();
        $router->add('p/{slug:slug}', static fn ($slug) => $slug);
        $router->match('GET', 'p/hello-world');

        $this->assertSame(['slug' => 'hello-world'], $router->getRequestParams()['params']);
    }

    public function testCustomRegexIsHonored(): void
    {
        $router = new Router();
        $router->add('posts/{id:[a-z]+}', static fn ($id) => $id);
        $router->match('GET', 'posts/abc');
        $this->assertSame(['id' => 'abc'], $router->getRequestParams()['params']);

        $rejected = new Router();
        $rejected->add('posts/{id:[a-z]+}', static fn ($id) => $id);
        $rejected->match('GET', 'posts/123');
        $this->assertFalse($rejected->getRequestParams());
    }

    public function testUnmatchedRouteReturnsFalse(): void
    {
        $router = new Router();
        $router->add('/', 'PageController@show');
        $router->match('GET', '/missing');

        $this->assertFalse($router->getRequestParams());
    }

    public function testMatchClearsPreviousResultWhenTheNextPathDoesNotMatch(): void
    {
        $router = new Router();
        $router->add('posts/{postId:id}', static fn ($postId) => $postId);
        $router->match('GET', 'posts/10');

        $this->assertSame(['postId' => '10'], $router->getRequestParams()['params']);

        $router->match('GET', 'posts/01');

        $this->assertFalse($router->getRequestParams());
    }

    public function testUnknownMethodsAreIgnoredWhenAdding(): void
    {
        $router = new Router();
        $router->add('/only-get', 'PageController@show', 'GET|PATCH');
        $router->match('GET', 'only-get');

        $this->assertNotFalse($router->getRequestParams());
    }

    public function testSettersUpdateMatchingBehavior(): void
    {
        $router = new Router();
        $router->setAllowedMethods(['GET', 'PUT']);
        $router->add('/item', 'ItemController@update', ['PUT']);
        $router->setBasePath('/v1/');
        $router->match('PUT', '/v1/item');

        $this->assertNotFalse($router->getRequestParams());
    }
}
