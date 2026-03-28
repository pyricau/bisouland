<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui2;

use Bl\Qa\UserInterface\Tui\QalinBanner;
use Symfony\Component\Tui\Style\Style;
use Symfony\Component\Tui\Widget\ContainerWidget;
use Symfony\Component\Tui\Widget\TextWidget;

final readonly class QalinLayout
{
    /**
     * @param array<string, string> $hints key => action
     */
    public static function create(array $hints): ContainerWidget
    {
        $containerWidget = new ContainerWidget();
        $containerWidget->add(self::banner());
        $containerWidget->add(new ContainerWidget()->expandVertically(true));
        $containerWidget->add(self::footer($hints));

        return $containerWidget;
    }

    private static function banner(): ContainerWidget
    {
        $logo = new TextWidget(implode("\n", QalinBanner::LOGO));
        $logo->addStyleClass('logo');

        $slogan = new ContainerWidget();
        foreach (QalinBanner::SLOGAN as $i => $line) {
            $widget = new TextWidget($line);
            if (0 === $i) {
                $widget->addStyleClass('slogan-title');
            }

            $slogan->add($widget);
        }

        $banner = new ContainerWidget();
        $banner->addStyleClass('banner');
        $banner->add($logo);
        $banner->add($slogan);

        return $banner;
    }

    /**
     * @param array<string, string> $hints key => action
     */
    private static function footer(array $hints): TextWidget
    {
        $keyStyle = new Style()->withColor('blue')->withBold();
        $actionStyle = new Style()->withColor('gray');

        $parts = [];
        foreach ($hints as $key => $action) {
            $parts[] = $keyStyle->apply("[{$key}]").' '.$actionStyle->apply($action);
        }

        return new TextWidget(implode('  ', $parts));
    }
}
