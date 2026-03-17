<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Infrastructure\PhpTui\Component\Layout;

use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\HotkeyTab\HotkeyTabsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Layout\LayoutWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutWidget::class)]
#[Small]
final class LayoutWidgetTest extends TestCase
{
    #[TestDox('It has banner (e.g. BannerWidget)')]
    public function test_it_has_banner(): void
    {
        $banner = BannerWidget::from([]);
        $widget = LayoutWidget::from($banner, HotkeyTabsWidget::fromTabs(['1' => 'Nav']), ParagraphWidget::fromString(''), KeyHintsWidget::from(['F' => 'f']));

        $this->assertSame($banner, $widget->banner);
    }

    #[TestDox('It has navbar (e.g. HotkeyTabsWidget)')]
    public function test_it_has_navbar(): void
    {
        $navbar = HotkeyTabsWidget::fromTabs(['1' => 'Nav']);
        $widget = LayoutWidget::from(BannerWidget::from([]), $navbar, ParagraphWidget::fromString(''), KeyHintsWidget::from(['F' => 'f']));

        $this->assertSame($navbar, $widget->navbar);
    }

    #[TestDox('It has body (e.g. ParagraphWidget)')]
    public function test_it_has_body(): void
    {
        $body = ParagraphWidget::fromString('Body');
        $widget = LayoutWidget::from(BannerWidget::from([]), HotkeyTabsWidget::fromTabs(['1' => 'Nav']), $body, KeyHintsWidget::from(['F' => 'f']));

        $this->assertSame($body, $widget->body);
    }

    #[TestDox('It has footer (e.g. KeyHintsWidget)')]
    public function test_it_has_footer(): void
    {
        $footer = KeyHintsWidget::from(['Foot' => 'F']);
        $widget = LayoutWidget::from(BannerWidget::from([]), HotkeyTabsWidget::fromTabs(['1' => 'Nav']), ParagraphWidget::fromString(''), $footer);

        $this->assertSame($footer, $widget->footer);
    }
}
