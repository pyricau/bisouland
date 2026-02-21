<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Cli\Scenario;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignInNewPlayer;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignInNewPlayerHandler;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'scenario:sign-in-new-player',
    description: 'Signs up a new player and immediately signs them in',
)]
final readonly class SignInNewPlayerCommand
{
    public function __construct(
        private SignInNewPlayerHandler $signInNewPlayerHandler,
    ) {
    }

    public function __invoke(
        #[Argument(description: '4-15 alphanumeric characters or underscores')]
        string $username,
        #[Argument(description: '8+ printable characters')]
        string $password,
        SymfonyStyle $io,
    ): int {
        try {
            $output = $this->signInNewPlayerHandler->run(new SignInNewPlayer($username, $password));
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Successfully signed up and signed in new player');

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
