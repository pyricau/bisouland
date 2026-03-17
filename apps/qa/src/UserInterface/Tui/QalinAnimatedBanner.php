<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui;

use Bl\Qa\Infrastructure\PhpTui\Component\Banner\BannerWidget;
use Bl\Qa\UserInterface\Tui\QalinAnimatedBanner\Animation;
use Bl\Qa\UserInterface\Tui\QalinAnimatedBanner\Beat;
use Bl\Qa\UserInterface\Tui\QalinAnimatedBanner\Sparkles;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Style\Style;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final class QalinAnimatedBanner
{
    /**
     * @var list<Animation>
     */
    private array $animations;

    private ?Animation $currentAnimation = null;

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
        $this->animations = [
            new Beat($this->clock),
            new Sparkles($this->clock),
        ];
    }

    public function animate(): void
    {
        $this->currentAnimation = $this->pickRandomAnimation();
        $this->currentAnimation->animate();
    }

    public function widget(): BannerWidget
    {
        $logo = $this->currentAnimation?->logo()
            ?? QalinBanner::LOGO;
        $logoStyle = $this->currentAnimation?->logoStyle()
            ?? Style::default()->fg(AnsiColor::Red);

        return QalinBanner::widgetWithLogo($logo, $logoStyle);
    }

    private function pickRandomAnimation(): Animation
    {
        $count = \count($this->animations);
        if (0 === $count) {
            throw new \LogicException('No animations registered.');
        }

        return $this->animations[random_int(0, $count - 1)];
    }
}
