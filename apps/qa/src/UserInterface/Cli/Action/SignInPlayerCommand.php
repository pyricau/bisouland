<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Cli\Action;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayer;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayerHandler;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'action:sign-in-player',
    description: 'Sign in an existing player',
)]
final readonly class SignInPlayerCommand
{
    public function __construct(
        private SignInPlayerHandler $signInPlayerHandler,
    ) {
    }

    public function __invoke(
        #[Argument(description: '4-15 alphanumeric characters')]
        string $username,
        SymfonyStyle $io,
    ): int {
        try {
            $output = $this->signInPlayerHandler->run(new SignInPlayer($username));
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Successfully completed Sign In Player');

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
