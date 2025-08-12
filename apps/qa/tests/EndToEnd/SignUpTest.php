<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\EndToEnd;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class SignUpTest extends TestCase
{
    public function test_it_allows_visitors_to_become_players(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'test_sign_up';
        $password = 'password';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        // Checking and Cleanup
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :pseudo');
        $stmt->execute([
            'pseudo' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            1,
            $deletedRows,
            "Expected 1 newly signed up player to be deleted, but {$deletedRows} were deleted. Sign up has failed.",
        );
    }
}
