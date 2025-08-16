<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Infrastructure\Scenario;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;

final readonly class SignUpNewPlayer
{
    public static function run(
        string $username = 'BisouTest',
        string $password = 'password',
        string $passwordConfirmation = 'password',
    ): Player {
        $httpClient = TestKernelSingleton::get()->httpClient();

        if ('BisouTest' === $username) {
            $username = substr('BisouTest_'.uniqid(), 0, 15);
        }

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $passwordConfirmation,
                'inscription' => "S'inscrire",
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        return new Player($username, $password);
    }
}
