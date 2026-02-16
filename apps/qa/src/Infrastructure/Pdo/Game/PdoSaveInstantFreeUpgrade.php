<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Pdo\Game;

use Bl\Qa\Domain\Auth\Account;
use Bl\Qa\Domain\Auth\Account\AccountId;
use Bl\Qa\Domain\Auth\Account\PasswordHash;
use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Domain\Exception\ServerErrorException;
use Bl\Qa\Domain\Exception\ValidationFailedException;
use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Domain\Game\Player\CloudCoordinates;
use Bl\Qa\Domain\Game\Player\LovePoints;
use Bl\Qa\Domain\Game\Player\Score;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;
use Bl\Qa\Domain\Game\Player\UpgradableLevels\Upgradable;
use Bl\Qa\Domain\Game\SaveInstantFreeUpgrade;

/**
 * Upgrade logic:
 * - Find the player's current levels
 * - Check prerequisites (e.g. Leg requires Heart >= 15)
 * - Compute cost based on current levels
 * - Increment upgrade by one
 * - Add cost to score
 *
 * This logic is done in PHP (Upgradable enum),
 * and uses SELECT ... FOR UPDATE with transactions to ensure atomicity
 * and protect against race conditions (e.g. concurrent upgrades
 * reading stale levels before the first upgrade commits).
 *
 * A CTE alternative was considered,
 * but each upgradable needs its own cost formula and prerequisite check,
 * so the SQL query would have to be built dynamically per-case anyway.
 * At that point the domain rules belong on the Upgradable enum,
 * where adding a new upgradable is a single-file change with exhaustive match safety.
 */
final readonly class PdoSaveInstantFreeUpgrade implements SaveInstantFreeUpgrade
{
    private \PDOStatement $selectStmt;

    private string $updateStmtTemplate;

    public function __construct(
        private \PDO $pdo,
    ) {
        $this->selectStmt = $this->pdo->prepare(<<<'SQL'
            SELECT
                id AS account_id,
                pseudo AS username,
                mdp AS password_hash,
                amour AS love_points,
                score,
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
            FROM membres
            WHERE pseudo = :username
            FOR UPDATE
        SQL);
        $this->updateStmtTemplate = <<<'SQL'
            UPDATE membres
            SET %upgradable% = %upgradable% + 1,
                score = score + :cost
            WHERE pseudo = :username
            RETURNING
                id AS account_id,
                pseudo AS username,
                mdp AS password_hash,
                amour AS love_points,
                score,
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

    public function save(Username $username, Upgradable $upgradable): Player
    {
        $this->pdo->beginTransaction();

        try {
            $this->selectStmt->execute([
                'username' => $username->toString(),
            ]);

            /**
             * @var array{
             *      account_id: string,
             *      username: string,
             *      password_hash: string,
             *      love_points: int,
             *      score: int,
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
            $row = $this->selectStmt->fetch();
            if (false === $row) {
                throw ValidationFailedException::make(
                    "Invalid \"Username\" parameter: player not found (`{$username->toString()}` given)",
                );
            }

            $upgradableLevels = UpgradableLevels::fromArray($row);

            $upgradable->checkPrerequisites($upgradableLevels);

            $cost = $upgradable->computeCost($upgradableLevels);

            $updateStmt = $this->pdo->prepare(str_replace(
                '%upgradable%',
                $upgradable->dbColumn(),
                $this->updateStmtTemplate,
            ));

            try {
                $updateStmt->execute([
                    'cost' => $cost,
                    'username' => $username->toString(),
                ]);
            } catch (\PDOException $pdoException) {
                throw ServerErrorException::make(
                    "Failed to SaveInstantFreeUpgrade (`{$username->toString()}`, `{$upgradable->toString()}`): unexpected database error",
                    $pdoException,
                );
            }

            /**
             * @var array{
             *      account_id: string,
             *      username: string,
             *      password_hash: string,
             *      love_points: int,
             *      score: int,
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
             * } $updatedRow
             */
            $updatedRow = $updateStmt->fetch();

            $player = new Player(
                new Account(
                    AccountId::fromString($updatedRow['account_id']),
                    Username::fromString($updatedRow['username']),
                    PasswordHash::fromString($updatedRow['password_hash']),
                ),
                LovePoints::fromInt($updatedRow['love_points']),
                Score::fromInt($updatedRow['score']),
                CloudCoordinates::fromInts($updatedRow['cloud_coordinates_x'], $updatedRow['cloud_coordinates_y']),
                UpgradableLevels::fromArray($updatedRow),
            );

            $this->pdo->commit();

            return $player;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();

            throw $throwable;
        }
    }
}
