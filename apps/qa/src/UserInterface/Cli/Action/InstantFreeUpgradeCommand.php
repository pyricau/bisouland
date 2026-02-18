<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Cli\Action;

use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgrade;
use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgradeHandler;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'action:instant-free-upgrade',
    description: 'Instantly upgrade for free',
)]
final readonly class InstantFreeUpgradeCommand
{
    public function __construct(
        private InstantFreeUpgradeHandler $instantFreeUpgradeHandler,
    ) {
    }

    public function __invoke(
        #[Argument(description: 'an existing one')]
        string $username,
        #[Argument(description: 'an Organ (e.g. heart), Bisou (e.g. smooch) or Technique (e.g. hold_breath)')]
        string $upgradable,
        SymfonyStyle $io,
        #[Option(description: 'how many levels to upgrade at once')]
        int $levels = 1,
    ): int {
        try {
            $output = $this->instantFreeUpgradeHandler->run(new InstantFreeUpgrade($username, $upgradable, $levels));
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Successfully completed Instant Free Upgrade');

        $rows = [];
        foreach ($output->toArray() as $field => $value) {
            $rows[] = [$field, $value];
        }

        $table = new Table($io);
        $table->setStyle('markdown');
        $table->setHeaders(['Field', 'Value']);
        $table->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
