<?php

declare(strict_types=1);

namespace Tests\Unit;

use System\Classes\Config;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new Config();
    }

    public function testGetWithoutKeyReturnsAllAutoloadConfigs(): void
    {
        $all = $this->config->get();

        $this->assertArrayHasKey('system', $all);
        $this->assertArrayHasKey('providers', $all);
        $this->assertArrayHasKey('app', $all['system']);
    }

    public function testGetReturnsTopLevelFileConfig(): void
    {
        $system = $this->config->get('system');

        $this->assertIsArray($system);
        $this->assertArrayHasKey('app', $system);
    }

    public function testGetReadsNestedDotPath(): void
    {
        $this->assertIsString($this->config->get('system.app.name'));
        $this->assertNotSame('', $this->config->get('system.app.name'));
        $this->assertIsString($this->config->get('system.log.path'));
        $this->assertNotSame('', $this->config->get('system.log.path'));
        $this->assertIsString($this->config->get('system.log.level'));
        $this->assertNotSame('', $this->config->get('system.log.level'));
    }

    public function testGetReturnsDefaultWhenPathIsMissing(): void
    {
        $this->assertNull($this->config->get('system.missing'));
        $this->assertSame('n/a', $this->config->get('system.missing.deep', 'n/a'));
    }

    public function testProvidersConfigListsFrameworkProviders(): void
    {
        $providers = $this->config->get('providers');

        $this->assertContains(\System\Providers\AppServiceProvider::class, $providers);
        $this->assertContains(\System\Providers\RouteServiceProvider::class, $providers);
        $this->assertContains(\System\Providers\CaptchaServiceProvider::class, $providers);
    }
}
