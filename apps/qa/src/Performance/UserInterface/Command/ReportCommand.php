<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\UserInterface\Command;

use Bl\Qa\Performance\Application\UseCase\GetPerformanceReport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ReportCommand extends Command
{
    public function __construct(
        private readonly GetPerformanceReport $getPerformanceReport,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('metrics:report')
            ->setDescription('Show performance report')
            ->addOption(
                'hours',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of hours to analyze',
                '24'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $hoursOption = $input->getOption('hours');
        if (!\is_string($hoursOption)) {
            $hoursOption = '24';
        }
        $hours = (int) $hoursOption;

        $io->title("📊 Performance Report (Last {$hours} hours)");

        $summary = $this->getPerformanceReport->getSummary($hours);

        if ([] === $summary) {
            $io->warning("No performance data collected in the last {$hours} hours.");

            return Command::SUCCESS;
        }

        $io->section('Page Performance');

        $table = new Table($output);
        $table->setHeaders(['Page', 'Samples', 'Avg', 'Median', 'P95', 'P99']);

        foreach ($summary as $data) {
            $table->addRow([
                $data->page,
                $data->samples,
                "{$data->avgMs} ms",
                "{$data->medianMs} ms",
                "{$data->p95Ms} ms",
                "{$data->p99Ms} ms",
            ]);
        }

        $table->render();

        $io->section('🐌 Top 5 Slowest Pages (by P95)');

        $slowest = $this->getPerformanceReport->getSlowestPages($hours, 5);

        if ([] === $slowest) {
            $io->text('No slow pages found.');
        } else {
            foreach ($slowest as $i => $page) {
                $rank = $i + 1;
                $io->text("{$rank}. {$page->page}  P95: {$page->p95Ms} ms  Avg: {$page->avgMs} ms  ({$page->samples} samples)");
            }
        }

        $io->newLine();

        return Command::SUCCESS;
    }
}
