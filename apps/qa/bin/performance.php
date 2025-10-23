#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Bl\Qa\Performance\Application\UseCase\CompareMetrics;
use Bl\Qa\Performance\Application\UseCase\GetPerformanceReport;
use Bl\Qa\Performance\Application\UseCase\ListBenchmarkRuns;
use Bl\Qa\Performance\Application\UseCase\PruneMetrics;
use Bl\Qa\Performance\Infrastructure\Persistence\PdoPerformanceMetricsRepository;
use Bl\Qa\Performance\UserInterface\Command\CompareCommand;
use Bl\Qa\Performance\UserInterface\Command\HistoryCommand;
use Bl\Qa\Performance\UserInterface\Command\PruneCommand;
use Bl\Qa\Performance\UserInterface\Command\ReportCommand;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use Symfony\Component\Console\Application;

$kernel = TestKernelSingleton::get();
$pdo = $kernel->pdo();

// Infrastructure
$repository = new PdoPerformanceMetricsRepository($pdo);

// Application Use Cases
$getPerformanceReport = new GetPerformanceReport($repository);
$compareMetrics = new CompareMetrics($repository);
$listBenchmarkRuns = new ListBenchmarkRuns($repository);
$pruneMetrics = new PruneMetrics($repository);

// Console Application
$application = new Application('BisouLand Performance Monitoring');

$application->add(new ReportCommand($getPerformanceReport));
$application->add(new CompareCommand($compareMetrics));
$application->add(new HistoryCommand($listBenchmarkRuns));
$application->add(new PruneCommand($pruneMetrics));

$application->run();
