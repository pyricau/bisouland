<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Scenario\SignInNewPlayer;

/**
 * @object-type DataTransferObject
 */
final readonly class SignInNewPlayer
{
    public function __construct(
        public string $username,
        public string $password,
    ) {
    }
}
