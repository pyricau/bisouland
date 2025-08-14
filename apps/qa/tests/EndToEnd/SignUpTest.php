<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\EndToEnd;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
final class SignUpTest extends TestCase
{
    public function test_it_allows_visitors_to_become_players(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $username = 'test_sign_up00';
        $password = 'password';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        $this->assertSignedUpCount($username, 1);
    }

    #[DataProvider('invalidCredentialsProvider')]
    #[TestDox('It prevents invalid credentials: $description')]
    public function test_it_prevents_invalid_credentials(string $username, string $password, string $description): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        $this->assertSignedUpCount($username, 0);
    }

    /**
     * @return array<array{string, string, string}>
     */
    public static function invalidCredentialsProvider(): array
    {
        return [
            ['usr', 'password', 'username too short (< 4 characters)'],
            ['test_sign_up02__', 'password', 'username too long (> 15 characters)'],
            ['test_sign_up03!', 'password', 'username contains special characters (non alpha-numerical, not an underscore (`_`))'],
            ['test_sign_up05', 'pass', 'password too short (< 5 characters)'],
            ['test_sign_up06', 'passwordthatistoolong', 'password too long (> 15 characters)'],
            ['test_sign_up07', 'password!', 'password contains special characters (non alpha-numerical, not an underscore (`_`))'],
        ];
    }

    #[DataProvider('forbiddenUsernamesProvider')]
    #[TestDox('It prevents forbidden usernames: `$name` ($description)')]
    public function test_it_prevents_forbidden_usernames(string $name, string $description): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $password = 'password';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $name,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        $this->assertSignedUpCount($name, 0);
    }

    /**
     * @return array<array{string, string}>
     */
    public static function forbiddenUsernamesProvider(): array
    {
        return [
            ['BisouLand', 'system account, for notifications'],
        ];
    }

    #[TestDox('It prevents usernames that are already used')]
    public function test_it_prevents_usernames_that_are_already_used(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $username = 'test_sign_up04';
        $password = 'password';

        // First registration should succeed
        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);
        // Second registration should fail
        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        $this->assertSignedUpCount($username, 1);
    }

    public function test_it_prevents_passwords_that_do_not_match_confirmation(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();

        $username = 'test_sign_up08';
        $password = 'password';
        $passwordConfirmation = 'different';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $passwordConfirmation,
                'inscription' => "S'inscrire",
            ],
        ]);

        $this->assertSignedUpCount($username, 0);
    }

    private function assertSignedUpCount(string $username, int $expectedCount): void
    {
        $actualCount = $this->deleteSignedUpUsers($username);

        $this->assertSame(
            $expectedCount,
            $actualCount,
            "Failed asserting that Signed Up Count {$actualCount} is {$expectedCount}",
        );
    }

    private function deleteSignedUpUsers(string $username): int
    {
        $pdo = TestKernelSingleton::get()->pdo();

        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        return $deletedRows;
    }
}
