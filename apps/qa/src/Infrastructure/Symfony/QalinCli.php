<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Symfony;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A Symfony CLI Application, with Qalin custom help screen (when no commands provided).
 * Displays logo, slogan and list of qalin commands (actions and scenarios).
 */
final class QalinCli extends Application
{
    /**
     * width: 18
     * height: 6.
     *
     * @var array<string>
     */
    private const array LOGO = [
        '  ████      ████  ',
        '████████  ████████',
        '██████████████████',
        '██████████████████',
        '   ████████████   ',
        '      ██████      ',
    ];

    /** @var array<string> */
    private const array SLOGAN = [
        "Qalin (it's pronounced câlin)",
        'Quality Assurance Local Interface Nudger',
        '(your own Test Control Interface)',
    ];

    public function getHelp(): string
    {
        // Colour LOGO in red
        $lines = array_map(
            static fn (string $line): string => "<fg=red>{$line}</>",
            self::LOGO,
        );

        // Add SLOGAN to the right of LOGO
        $sloganMarginTop = 0;
        $sloganMarginLeft = '   ';
        foreach (self::SLOGAN as $i => $sloganLine) {
            // First line in BOLD
            $styleOpen = 0 === $i ? '<options=bold>' : '';
            $styleClose = 0 === $i ? '</>' : '';

            $lines[$sloganMarginTop + $i] .= "{$sloganMarginLeft}{$styleOpen}{$sloganLine}{$styleClose}";
        }

        return implode("\n", $lines);
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // Command provided, execute it
        if (null !== $this->getCommandName($input)) {
            return parent::doRun($input, $output);
        }

        // No command provided, display help (logo, slogan, qalin commands)
        $output->writeln($this->getHelp());
        $actions = [];
        $scenarios = [];
        foreach ($this->all() as $command) {
            $name = $command->getName() ?? '';
            // Group commands by prefix (action: and scenario:)
            // Skip Symfony default commands (help, list, _complete, completion)
            // which are added by Application::__construct() and cannot be filtered in qalin
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

        return Command::SUCCESS;
    }
}
