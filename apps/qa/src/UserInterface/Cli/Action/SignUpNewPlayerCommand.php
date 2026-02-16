<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Cli\Action;

use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayerHandler;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'action:sign-up-new-player',
    description: 'Signs up a new player for the given username and password',
)]
final class SignUpNewPlayerCommand extends Command
{
    public function __construct(
        private readonly SignUpNewPlayerHandler $signUpNewPlayerHandler,
    ) {
        parent::__construct();
    }

    public function __invoke(
        #[Argument(description: '4-15 alphanumeric characters or underscores')]
        string $username,
        #[Argument(description: '8+ printable characters')]
        string $password,
        SymfonyStyle $io,
    ): int {
        try {
            $output = $this->signUpNewPlayerHandler->run(new SignUpNewPlayer($username, $password));
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return self::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        $io->success('Successfully signed up new player');

        $rows = [];
        foreach ($output->toArray() as $field => $value) {
            $rows[] = [$field, $value];
        }

        $rows[] = ['password', '<redacted>'];

        $table = new Table($io);
        $table->setStyle('markdown');
        $table->setHeaders(['Field', 'Value']);
        $table->setRows($rows);
        $table->render();

        return self::SUCCESS;
    }
}
