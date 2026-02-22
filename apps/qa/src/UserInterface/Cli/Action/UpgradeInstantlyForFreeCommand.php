<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Cli\Action;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Qa\Application\Action\UpgradeInstantlyForFree\UpgradeInstantlyForFree;
use Bl\Qa\Application\Action\UpgradeInstantlyForFree\UpgradeInstantlyForFreeHandler;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'action:upgrade-instantly-for-free',
    description: 'Instantly upgrade for free',
)]
final readonly class UpgradeInstantlyForFreeCommand
{
    public function __construct(
        private UpgradeInstantlyForFreeHandler $upgradeInstantlyForFreeHandler,
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
            $upgradeInstantlyForFreed = $this->upgradeInstantlyForFreeHandler->run(new UpgradeInstantlyForFree($username, $upgradable, $levels));
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Successfully completed Upgrade Instantly For Free');

        $rows = [];
        foreach ($upgradeInstantlyForFreed->toArray() as $field => $value) {
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
