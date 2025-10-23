<?php

declare(strict_types=1);

namespace Bl\Qa\Performance\UserInterface\Command;

use Bl\Qa\Performance\Application\UseCase\PruneMetrics;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PruneCommand extends Command
{
    public function __construct(
        private readonly PruneMetrics $pruneMetrics,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('metrics:prune')
            ->setDescription('Clean up old performance metrics')
            ->addOption(
                'days',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of days to keep',
                '24'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $daysOption = $input->getOption('days');
        if (!\is_string($daysOption)) {
            $daysOption = '24';
        }
        $days = (int) $daysOption;

        $totalDeleted = $this->pruneMetrics->olderThan($days);

        $io->success("Pruned {$totalDeleted} old performance metrics (keeping last {$days} days)");

        return Command::SUCCESS;
    }
}
