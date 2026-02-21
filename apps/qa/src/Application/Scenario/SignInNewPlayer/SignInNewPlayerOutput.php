<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Scenario\SignInNewPlayer;

use Bl\Qa\Application\Action\SignInPlayer\SignInPlayerOutput;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayerOutput;
use Bl\Qa\Application\Scenario\ScenarioOutput;

/**
 * @object-type DataTransferObject
 */
final readonly class SignInNewPlayerOutput implements ScenarioOutput
{
    public function __construct(
        public SignUpNewPlayerOutput $signedUp,
        public SignInPlayerOutput $signedIn,
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
