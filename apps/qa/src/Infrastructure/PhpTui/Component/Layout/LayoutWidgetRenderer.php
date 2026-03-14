<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\PhpTui\Component\Layout;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

/**
 * Renders LayoutWidget as a vertical grid of four rounded-border sections: banner, navbar, body, footer.
 *
 * Section heights come from each widget's constraint() (banner, navbar, footer),
 * except body which always fills the remaining space (Constraint::min(0)).
 *
 * The body section has horizontal and bottom padding (left: 1, right: 1, bottom: 1).
 *
 * Registration (child renderers for banner, navbar, body, footer widgets are required):
 *     $display = DisplayBuilder::default($backend)
 *         ->addWidgetRenderer(new LayoutWidgetRenderer())
 *         ->addWidgetRenderer(new BannerWidgetRenderer())
 *         ->build();
 */
final class LayoutWidgetRenderer implements WidgetRenderer
{
    public function render(
        WidgetRenderer $renderer,
        Widget $widget,
        Buffer $buffer,
        Area $area,
    ): void {
        if (!$widget instanceof LayoutWidget) {
            return;
        }

        $renderer->render(
            $renderer,
            GridWidget::default()
                ->direction(Direction::Vertical)
                ->constraints(
                    $widget->banner->constraint(),
                    $widget->navbar->constraint(),
                    Constraint::min(0),
                    $widget->footer->constraint(),
                )
                ->widgets(
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->widget($widget->banner),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->widget($widget->navbar),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->padding(Padding::fromScalars(left: 1, right: 1, top: 0, bottom: 1))
                        ->widget($widget->body),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->widget($widget->footer),
                ),
            $buffer,
            $area,
        );
    }
}
