<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Monolith\Infrastructure\Scenario;

use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;

final readonly class SignUpNewPlayer
{
    public static function run(
        string $username,
        string $password,
        string $passwordConfirmation,
    ): string {
        $httpClient = TestKernelSingleton::get()->httpClient();

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

        return $username;
    }
}
