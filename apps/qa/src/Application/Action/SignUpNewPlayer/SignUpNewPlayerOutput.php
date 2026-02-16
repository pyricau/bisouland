<?php

declare(strict_types=1);

namespace Bl\Qa\Application\Action\SignUpNewPlayer;

use Bl\Qa\Application\Action\ActionOutput;
use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;

/**
 * @object-type DataTransferObject
 */
final readonly class SignUpNewPlayerOutput implements ActionOutput
{
    public function __construct(
        public Player $player,
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        $data = [
            'account_id' => $this->player->account->accountId->toString(),
            'username' => $this->player->account->username->toString(),
            'love_points' => $this->player->lovePoints->toInt(),
            'score' => $this->player->score->toInt(),
            'cloud_coordinates_x' => $this->player->cloudCoordinates->getX(),
            'cloud_coordinates_y' => $this->player->cloudCoordinates->getY(),
        ];
        foreach (Upgradable::cases() as $upgradable) {
            $data[$upgradable->value] = $this->player->upgradableLevels->toInt($upgradable);
        }

        return $data;
    }
}
