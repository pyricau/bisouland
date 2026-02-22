<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Scenario\SignInNewPlayer;

use Bl\Qa\Application\Action\SignInPlayer\SignedInPlayer;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignedUpNewPlayer;
use Bl\Qa\Application\Output;

/**
 * @object-type DataTransferObject
 */
final readonly class SignedInNewPlayer implements Output
{
    public function __construct(
        public SignedUpNewPlayer $signedUp,
        public SignedInPlayer $signedIn,
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [...$this->signedUp->toArray(), ...$this->signedIn->toArray()];
    }
}
