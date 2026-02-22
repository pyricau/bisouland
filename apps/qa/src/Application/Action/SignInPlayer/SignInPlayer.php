<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\SignInPlayer;

/**
 * @object-type DataTransferObject
 */
final readonly class SignInPlayer
{
    public function __construct(
        public string $username,
    ) {
    }
}
