<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Pdo\Game;

use Bl\Qa\Domain\Auth\Account;
use Bl\Qa\Domain\Auth\Account\AccountId;
use Bl\Qa\Domain\Auth\Account\PasswordHash;
use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\ApplyCompletedUpgrade;
use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Domain\Game\Player\CloudCoordinates;
use Bl\Qa\Domain\Game\Player\LovePoints;
use Bl\Qa\Domain\Game\Player\MilliScore;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;
use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;

final readonly class PdoApplyCompletedUpgrade implements ApplyCompletedUpgrade
{
    private string $updateStmtTemplate;

    public function __construct(
        private \PDO $pdo,
    ) {
        $this->updateStmtTemplate = <<<'SQL'
            UPDATE membres
            SET %upgradable% = %upgradable% + 1,
                score = score + :milli_score
            WHERE pseudo = :username
            RETURNING
                id AS account_id,
                pseudo AS username,
                mdp AS password_hash,
                amour AS love_points,
                score AS milli_score,
                nuage AS cloud_coordinates_x,
                position AS cloud_coordinates_y,
                coeur AS heart,
                bouche AS mouth,
                langue AS tongue,
                dent AS teeth,
                jambes AS legs,
                oeil AS eyes,
                smack AS peck,
                baiser AS smooch,
                pelle AS french_kiss,
                tech1 AS hold_breath,
                tech2 AS flirt,
                tech3 AS spit,
                tech4 AS leap,
                soupe AS soup
        SQL;
    }

    public function apply(Username $username, Upgradable $upgradable, int $milliScore): Player
    {
        $updateStmt = $this->pdo->prepare(str_replace(
            '%upgradable%',
            $upgradable->dbColumn(),
            $this->updateStmtTemplate,
        ));

        try {
            $updateStmt->execute([
                'milli_score' => $milliScore,
                'username' => $username->toString(),
            ]);
        } catch (\PDOException $pdoException) {
            throw ServerErrorException::make(
                'Failed to ApplyCompletedUpgrade'
                ." (`{$username->toString()}`, `{$upgradable->toString()}`, `{$milliScore}`)"
                .': unexpected database error',
                $pdoException,
            );
        }

        /**
         * @var array{
         *      account_id: string,
         *      username: string,
         *      password_hash: string,
         *      love_points: int,
         *      milli_score: int,
         *      cloud_coordinates_x: int,
         *      cloud_coordinates_y: int,
         *      heart: int,
         *      mouth: int,
         *      tongue: int,
         *      teeth: int,
         *      legs: int,
         *      eyes: int,
         *      peck: int,
         *      smooch: int,
         *      french_kiss: int,
         *      hold_breath: int,
         *      flirt: int,
         *      spit: int,
         *      leap: int,
         *      soup: int,
         * }|false $row
         */
        $row = $updateStmt->fetch();
        if (false === $row) {
            throw ValidationFailedException::make(
                "Invalid \"Username\" parameter: it should be an already existing one (`{$username->toString()}` given)",
            );
        }

        return new Player(
            new Account(
                AccountId::fromString($row['account_id']),
                Username::fromString($row['username']),
                PasswordHash::fromString($row['password_hash']),
            ),
            LovePoints::fromInt($row['love_points']),
            MilliScore::fromInt($row['milli_score']),
            CloudCoordinates::fromInts($row['cloud_coordinates_x'], $row['cloud_coordinates_y']),
            UpgradableLevels::fromArray($row),
        );
    }
}
