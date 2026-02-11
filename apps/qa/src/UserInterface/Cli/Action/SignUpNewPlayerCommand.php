<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Cli\Action;

use Bl\Qa\Application\Action\SignUpNewPlayer;
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
        private readonly SignUpNewPlayer $signUpNewPlayer,
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
            $player = $this->signUpNewPlayer->run($username, $password);
        } catch (ValidationFailedException $e) {
            $io->error($e->getMessage());

            return self::INVALID;
        } catch (ServerErrorException $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        $io->success('Successfully signed up new player');
        $table = new Table($io);
        $table->setStyle('markdown');
        $table->setHeaders(['Field', 'Value']);
        $table->setRows([
            ['account_id', $player->account->accountId->toString()],
            ['username', $player->account->username->toString()],
            ['password', '<redacted>'],
            ['love_points', $player->lovePoints->toInt()],
            ['score', $player->score->toInt()],
            ['cloud_coordinates_x', $player->cloudCoordinates->getX()],
            ['cloud_coordinates_y', $player->cloudCoordinates->getY()],
        ]);
        $table->render();

        return self::SUCCESS;
    }
}
