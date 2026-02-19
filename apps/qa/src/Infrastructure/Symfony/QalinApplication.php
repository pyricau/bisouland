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
            $output->writeln('<fg=yellow>Available commands:</>');
            foreach ($this->all() as $command) {
                $name = $command->getName();
                if (str_starts_with($name, 'action:') || str_starts_with($name, 'scenario:')) {
                    $output->writeln(sprintf('  <info>%-35s</info> %s', $name, $command->getDescription()));
                }
            }

            return 0;
        }

        return parent::doRun($input, $output);
    }
}
