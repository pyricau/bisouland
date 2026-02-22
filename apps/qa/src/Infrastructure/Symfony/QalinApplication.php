<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Symfony;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class QalinApplication extends Application
{
    public function getHelp(): string
    {
        return implode("\n", [
            '',
            '  <fg=red>████</>      <fg=red>████</>     <options=bold>Qalin</> (it\'s pronounced <options=bold>câlin</>)',
            '<fg=red>████████</>  <fg=red>████████</>   Quality Assurance Local Interface Nudger',
            '<fg=red>██████████████████</>   (your own Test Control Interface)',
            '<fg=red>██████████████████</>',
            '   <fg=red>████████████</>  ',
            '      <fg=red>██████</>     ',
            '',
        ]);
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->getCommandName($input)) {
            $output->writeln($this->getHelp());
            $actions = [];
            $scenarios = [];
            foreach ($this->all() as $command) {
                $name = $command->getName() ?? '';
                match (true) {
                    str_starts_with($name, 'action:') => $actions[] = $command,
                    str_starts_with($name, 'scenario:') => $scenarios[] = $command,
                    default => null,
                };
            }

            $output->writeln('<fg=yellow>Available actions:</>');
            foreach ($actions as $action) {
                $padded = str_pad($action->getName() ?? '', 35);
                $output->writeln("  <info>{$padded}</info> {$action->getDescription()}");
            }

            $output->writeln('');
            $output->writeln('<fg=yellow>Available scenarios:</>');
            foreach ($scenarios as $scenario) {
                $padded = str_pad($scenario->getName() ?? '', 35);
                $output->writeln("  <info>{$padded}</info> {$scenario->getDescription()}");
            }

            return 0;
        }

        return parent::doRun($input, $output);
    }
}
