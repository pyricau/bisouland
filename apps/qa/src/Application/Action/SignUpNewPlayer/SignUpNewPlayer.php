<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\SignUpNewPlayer;

/**
 * @object-type DataTransferObject
 */
final readonly class SignUpNewPlayer
{
    public function __construct(
        public string $username,
        public string $password,
    ) {
    }
}
