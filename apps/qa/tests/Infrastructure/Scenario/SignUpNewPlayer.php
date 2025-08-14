<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure\Scenario;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;

final readonly class SignUpNewPlayer
{
    public static function run(): Player
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $username = substr('BisouTest_'.uniqid(), 0, 15);
        $password = 'password';
        $passwordHash = md5($password);
        $timestamp = time();

        try {
            $stmt = $pdo->prepare('
                INSERT INTO membres (pseudo, mdp, confirmation, timestamp, lastconnect)
                VALUES (?, ?, 1, ?, ?)
            ');
            $stmt->execute([$username, $passwordHash, $timestamp, $timestamp]);
        } catch (\PDOException $e) {
            // Ignore duplicate entry errors - user already exists, which is fine
            if ('23000' !== $e->getCode()) {
                throw $e;
            }
        }

        return new Player($username, $password);
    }
}
