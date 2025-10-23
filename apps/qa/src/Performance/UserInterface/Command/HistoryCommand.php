<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\UserInterface\Command;

use Bl\Qa\Performance\Application\UseCase\ListBenchmarkRuns;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class HistoryCommand extends Command
{
    public function __construct(
        private readonly ListBenchmarkRuns $listBenchmarkRuns,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('metrics:history')
            ->setDescription('Show benchmark run history')
            ->addOption(
                'days',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of days to show',
                '30'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $daysOption = $input->getOption('days');
        if (!\is_string($daysOption)) {
            $daysOption = '30';
        }
        $days = (int) $daysOption;

        $io->title('📊 Benchmark Run History');

        $runs = $this->listBenchmarkRuns->forLastDays($days);

        if ([] === $runs) {
            $io->warning("No benchmark runs found in the last {$days} days.");
            $io->text('Run "make benchmark" to generate performance data.');

            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Run Time', 'Samples', 'Duration']);

        foreach ($runs as $run) {
            $table->addRow([
                $run->runTime,
                $run->samples,
                "{$run->durationSeconds()}s",
            ]);
        }

        $table->render();

        if (\count($runs) >= 2) {
            $io->newLine();
            $io->section('💡 Suggested Comparison');

            $latest = $runs[0];
            $previous = $runs[1];

            // Calculate midpoint between the two runs
            $midpoint = (int) (($latest->startTimestamp + $previous->startTimestamp) / 2);
            $midpointTime = date('Y-m-d H:i:s', $midpoint);

            // Calculate appropriate time window (half the gap, minimum 1 hour)
            $gapSeconds = $latest->startTimestamp - $previous->startTimestamp;
            $hoursWindow = max(1, (int) ceil($gapSeconds / 7200)); // Half the gap in hours, rounded up

            $io->text('To compare the two most recent runs:');
            $io->text("  make benchmark-compare arg='--datetime=\"{$midpointTime}\" --hours={$hoursWindow}'");
        }

        return Command::SUCCESS;
    }
}
