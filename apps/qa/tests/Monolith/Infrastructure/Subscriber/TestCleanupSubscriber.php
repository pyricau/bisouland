<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure\Subscriber;

use Bl\Qa\Tests\Monolith\Infrastructure\Scenario\DeleteAllTestPlayers;
use PHPUnit\Event\TestRunner\Finished;
use PHPUnit\Event\TestRunner\FinishedSubscriber;

final readonly class TestCleanupSubscriber implements FinishedSubscriber
{
    public function notify(Finished $event): void
    {
        DeleteAllTestPlayers::run();
    }
}
