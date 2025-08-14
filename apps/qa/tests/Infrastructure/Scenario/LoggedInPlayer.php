<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure\Scenario;

final readonly class LoggedInPlayer
{
    public function __construct(
        public string $username,
        public string $password,
        public string $sessionCookie,
    ) {
    }
}
