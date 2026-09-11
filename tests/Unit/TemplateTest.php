<?php

declare(strict_types=1);

namespace Tests\Unit;

use League\Plates\Engine;
use System\Classes\Template;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    private Template $template;

    protected function setUp(): void
    {
        parent::setUp();
        $auth = $this->bootForViews();
        $this->template = new Template($auth, new Engine(ROOT . 'templates'));
    }

    public function testGetEngineReturnsPlatesEngine(): void
    {
        $this->assertInstanceOf(Engine::class, $this->template->getEngine());
        $this->assertTrue($this->template->getEngine()->exists('home/main'));
        $this->assertTrue($this->template->getEngine()->exists('404'));
    }

    public function testSetTitleAddsEscapedGlobal(): void
    {
        $html = $this->template->setTitle('<Home>')->render('home/main');

        $this->assertStringContainsString('&lt;Home&gt; | ', $html);
    }

    public function testAddGlobalAndAddDataAcceptArrays(): void
    {
        $this->template->addGlobal(['page_title' => 'Docs']);
        $html = $this->template->addData(['unused' => 'x'])->render('home/main');

        $this->assertStringContainsString('Docs | ', $html);
    }

    public function testRenderMergesCallTimeData(): void
    {
        $html = $this->template->render('404');

        $this->assertStringContainsString('404 Not Found!', $html);
        $this->assertStringContainsString('Liên kết bạn truy cập không tồn tại', $html);
    }

    public function testOutputEchoesRenderedTemplate(): void
    {
        ob_start();
        $this->template->setTitle('Missing')->output('404');
        $html = ob_get_clean();

        $this->assertStringContainsString('Missing | ', $html);
        $this->assertStringContainsString('404 Not Found!', $html);
    }

    public function testGuestLayoutShowsLoginLinks(): void
    {
        $html = $this->template->render('home/main');

        $this->assertStringContainsString('Đăng nhập', $html);
        $this->assertStringContainsString('Đăng ký', $html);
    }

    public function testLoggedInLayoutShowsAccountName(): void
    {
        $auth = $this->bootForViews($this->sampleUser());
        $template = new Template($auth, new Engine(ROOT . 'templates'));

        $html = $template->render('home/main');

        $this->assertStringContainsString('Xin chào <b>admin</b>', $html);
        $this->assertStringContainsString('Đăng xuất', $html);
    }
}
