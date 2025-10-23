<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\UserInterface\Command;

use Bl\Qa\Performance\Application\UseCase\CompareMetrics;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CompareCommand extends Command
{
    public function __construct(
        private readonly CompareMetrics $compareMetrics,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('metrics:compare')
            ->setDescription('Compare metrics before/after a datetime')
            ->addOption(
                'datetime',
                null,
                InputOption::VALUE_OPTIONAL,
                'Datetime to split before/after (e.g., "2024-10-14 15:30:00")',
                'now'
            )
            ->addOption(
                'hours',
                null,
                InputOption::VALUE_OPTIONAL,
                'Hours window for comparison',
                '1'
            )
            ->addOption(
                'threshold',
                null,
                InputOption::VALUE_OPTIONAL,
                'Similarity threshold percentage (default: 5.0, higher = more tolerance for noise)',
                '5.0'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $datetimeOption = $input->getOption('datetime');
        if (!\is_string($datetimeOption)) {
            $datetimeOption = 'now';
        }
        $datetime = $datetimeOption;

        $hoursOption = $input->getOption('hours');
        if (!\is_string($hoursOption)) {
            $hoursOption = '1';
        }
        $hours = (int) $hoursOption;

        $thresholdOption = $input->getOption('threshold');
        if (!\is_string($thresholdOption)) {
            $thresholdOption = '5.0';
        }
        $threshold = (float) $thresholdOption;

        $io->title('📊 Performance Comparison: Before vs After');

        $comparison = $this->compareMetrics->beforeAfter($datetime, $hours, $threshold);

        if ([] === $comparison) {
            $io->warning('No comparison data available.');

            return Command::SUCCESS;
        }

        $improved = [];
        $degraded = [];
        $similar = [];

        foreach ($comparison as $data) {
            $icon = match ($data->status) {
                'improved' => '🟢',
                'degraded' => '🔴',
                default => '⚪',
            };

            $result = "{$icon} {$data->page}"
                ." avg: {$data->avgDiffMs} ms ({$data->avgDiffPercent}%)"
                ." p95: {$data->p95DiffMs} ms ({$data->p95DiffPercent}%)";

            match ($data->status) {
                'improved' => $improved[] = $result,
                'degraded' => $degraded[] = $result,
                default => $similar[] = $result,
            };
        }

        if ([] !== $improved) {
            $io->section('🟢 Improved');
            $io->listing($improved);
        }
        if ([] !== $degraded) {
            $io->section('🔴 Degraded');
            $io->listing($degraded);
        }
        if ([] !== $similar) {
            $io->section("⚪ Similar (within ±{$threshold}%)");
            $io->listing($similar);
        }

        return Command::SUCCESS;
    }
}
