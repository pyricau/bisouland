<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Pdo\Game;

use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Qa\Domain\Auth\Account;
use Bl\Qa\Domain\Auth\Account\AccountId;
use Bl\Qa\Domain\Auth\Account\PasswordHash;
use Bl\Qa\Domain\Auth\Account\PasswordPlain;
use Bl\Qa\Domain\Auth\Account\Username;
use Bl\Qa\Domain\Game\Player;
use Bl\Qa\Domain\Game\Player\CloudCoordinates;
use Bl\Qa\Domain\Game\Player\LovePoints;
use Bl\Qa\Domain\Game\Player\MilliScore;
use Bl\Qa\Domain\Game\Player\UpgradableLevels;
use Bl\Qa\Domain\Game\SaveNewPlayer;

/**
 * Cloud placement logic:
 * - Find the last cloud (highest cloud number among existing players)
 *     - If it has 9 or more players, move to the next cloud (makes a new one)
 * - Pick a random available fluffy spot (1-16) on that cloud
 *
 * This logic is done in a single SQL query to ensure atomicity
 * and protect against race conditions (e.g. concurrent sign-ups
 * competing for the same cloud coordinate Y).
 */
final readonly class PdoSaveNewPlayer implements SaveNewPlayer
{
    /** @see https://www.postgresql.org/docs/current/errcodes-appendix.html */
    private const string PG_NOT_NULL_VIOLATION = '23502';

    /** @see https://www.postgresql.org/docs/current/errcodes-appendix.html */
    private const string PG_UNIQUE_VIOLATION = '23505';

    private \PDOStatement $stmt;

    public function __construct(
        private \PDO $pdo,
    ) {
        $this->stmt = $this->pdo->prepare(<<<'SQL'
            WITH last_cloud_coordinates_x AS (
                SELECT COALESCE(MAX(nuage), 1) AS cloud_coordinates_x
                FROM membres
            ),
            total_players_on_last_cloud AS (
                SELECT COUNT(*) AS total
                FROM membres
                WHERE nuage = (SELECT cloud_coordinates_x FROM last_cloud_coordinates_x)
            ),
            available_cloud_coordinates_x AS (
                SELECT CASE
                    WHEN (SELECT total FROM total_players_on_last_cloud) >= 9
                    THEN (SELECT cloud_coordinates_x FROM last_cloud_coordinates_x) + 1
                    ELSE (SELECT cloud_coordinates_x FROM last_cloud_coordinates_x)
                END AS cloud_coordinates_x
            ),
            available_cloud_coordinates_y AS (
                SELECT cloud_coordinates_y
                FROM generate_series(1, 16) AS cloud_coordinates_y
                WHERE cloud_coordinates_y NOT IN (
                    SELECT position FROM membres WHERE nuage = (SELECT cloud_coordinates_x FROM available_cloud_coordinates_x)
                )
                ORDER BY random()
                LIMIT 1
            )
            INSERT INTO membres (id, pseudo, mdp, amour, score, nuage, position)
            VALUES (
                :account_id,
                :username,
                :password_hash,
                :love_points,
                :milli_score,
                (SELECT cloud_coordinates_x FROM available_cloud_coordinates_x),
                (SELECT cloud_coordinates_y FROM available_cloud_coordinates_y)
            )
            RETURNING
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
        SQL);
    }

    public function save(Username $username, PasswordPlain $password): Player
    {
        $accountId = AccountId::create();
        $passwordHash = PasswordHash::fromPasswordPlain($password);

        try {
            $this->stmt->execute([
                'account_id' => $accountId->toString(),
                'username' => $username->toString(),
                'password_hash' => $passwordHash->toString(),
                'love_points' => LovePoints::STARTING_LOVE_POINTS,
                'milli_score' => MilliScore::STARTING_MILLI_SCORE,
            ]);
        } catch (\PDOException $pdoException) {
            $code = $pdoException->getCode();
            $message = $pdoException->getMessage();

            match (true) {
                self::PG_UNIQUE_VIOLATION === $code && str_contains($message, 'pseudo')
                    => throw ValidationFailedException::make(
                        "Invalid \"Username\" parameter: it is already registered (`{$username->toString()}` given)",
                        $pdoException,
                    ),
                self::PG_UNIQUE_VIOLATION === $code && str_contains($message, 'nuage')
                    => throw ValidationFailedException::make(
                        'Invalid "CloudCoordinates" parameter: X/Y are already occupied (race condition)',
                        $pdoException,
                    ),
                self::PG_NOT_NULL_VIOLATION === $code && str_contains($message, 'position')
                    => throw ValidationFailedException::make(
                        'Invalid "CloudCoordinates" parameter: no Y available (cloud is full)',
                        $pdoException,
                    ),
                default => throw ServerErrorException::make(
                    "Failed to SaveNewPlayer (`{$username->toString()}`): unexpected database error",
                    $pdoException,
                ),
            };
        }

        /**
         * @var array{
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
         * } $row
         */
        $row = $this->stmt->fetch();

        return new Player(
            new Account(
                $accountId,
                $username,
                $passwordHash,
            ),
            LovePoints::fromInt($row['love_points']),
            MilliScore::fromInt($row['milli_score']),
            CloudCoordinates::fromInts($row['cloud_coordinates_x'], $row['cloud_coordinates_y']),
            UpgradableLevels::fromArray($row),
        );
    }
}
