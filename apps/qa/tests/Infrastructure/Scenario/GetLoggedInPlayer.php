<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure\Scenario;

final class GetLoggedInPlayer
{
    private static ?LoggedInPlayer $loggedInPlayer = null;

    public static function run(): LoggedInPlayer
    {
        if (!self::$loggedInPlayer instanceof LoggedInPlayer) {
            $player = SignUpNewPlayer::run();
            $sessionCookie = LogInPlayer::run($player);

            self::$loggedInPlayer = new LoggedInPlayer($player->username, $player->password, $sessionCookie);
        }

        return self::$loggedInPlayer;
    }
}
