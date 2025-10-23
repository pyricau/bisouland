<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Performance\Integration\UserInterface\Command;

use Bl\Qa\Performance\Application\UseCase\PruneMetrics;
use Bl\Qa\Performance\Infrastructure\Persistence\PdoPerformanceMetricsRepository;
use Bl\Qa\Performance\UserInterface\Command\PruneCommand;
use Bl\Qa\Tests\Performance\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\ApplicationTester;

#[CoversClass(PruneCommand::class)]
final class PruneCommandTest extends TestCase
{
    private function getApplication(): ApplicationTester
    {
        $kernel = TestKernelSingleton::get();
        $pdo = $kernel->pdo();

        $repository = new PdoPerformanceMetricsRepository($pdo);
        $pruneMetrics = new PruneMetrics($repository);

        $application = new Application('BisouLand Performance Monitoring');
        $application->add(new PruneCommand($pruneMetrics));
        $application->setAutoExit(false);

        return new ApplicationTester($application);
    }

    #[Test]
    public function it_prunes_old_metrics_with_default_days(): void
    {
        $app = $this->getApplication();

        $input = [
            'metrics:prune',
        ];

        $statusCode = $app->run($input);

        self::assertSame(Command::SUCCESS, $statusCode, $app->getDisplay());
    }

    #[Test]
    public function it_prunes_old_metrics_with_custom_days(): void
    {
        $app = $this->getApplication();

        $input = [
            'metrics:prune',
            '--days' => '7',
        ];

        $statusCode = $app->run($input);

        self::assertSame(Command::SUCCESS, $statusCode, $app->getDisplay());
    }
}
