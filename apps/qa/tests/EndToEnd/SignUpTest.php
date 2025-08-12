<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\EndToEnd;

use Bl\Qa\Tests\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
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
        $pdo = TestKernelSingleton::get()->pdo();

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

        // Checking and Cleanup
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            1,
            $deletedRows,
            "Failed asserting that sign up succeeded. Expected 1 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    #[TestDox('It prevents usernames that are too short (< 4 characters)')]
    public function test_it_prevents_usernames_that_are_too_short(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'usr';
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
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            0,
            $deletedRows,
            "Failed asserting that Username validation succeeded. Expected 0 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    #[TestDox('It prevents usernames that are too long (> 15 characters)')]
    public function test_it_prevents_usernames_that_are_too_long(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'test_sign_up02__';
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
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            0,
            $deletedRows,
            "Failed asserting that Username validation succeeded. Expected 0 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    #[TestDox('It prevents usernames that have special characters (allowed: alpha numerical characters, and undersocre (`_`))')]
    public function test_it_prevents_usernames_that_have_special_characters(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'test_sign_up03!';
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
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            0,
            $deletedRows,
            "Failed asserting that Username validation succeeded. Expected 0 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    #[TestDox('It prevents usernames that are already used')]
    public function test_it_prevents_usernames_that_are_already_used(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'test_sign_up04';
        $password = 'password';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);
        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        // Checking and Cleanup
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            1,
            $deletedRows,
            "Failed asserting that Username validation succeeded. Expected 1 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    #[TestDox('It prevents passwords that are too short (< 5 characters)')]
    public function test_it_prevents_passwords_that_are_too_short(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'test_sign_up05';
        $password = 'pass';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        // Checking and Cleanup
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            0,
            $deletedRows,
            "Failed asserting that Password validation succeeded. Expected 0 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    #[TestDox('It prevents passwords that are too long (> 15 characters)')]
    public function test_it_prevents_passwords_that_are_too_long(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'test_sign_up06';
        $password = 'passwordthatistoolong';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        // Checking and Cleanup
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            0,
            $deletedRows,
            "Failed asserting that Password validation succeeded. Expected 0 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    #[TestDox('It prevents passwords that have special characters (allowed: alpha numerical characters, and undersocre (`_`))')]
    public function test_it_prevents_passwords_that_have_special_characters(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

        $username = 'test_sign_up07';
        $password = 'password!';

        $httpClient->request('POST', '/inscription.html', [
            'body' => [
                'Ipseudo' => $username,
                'Imdp' => $password,
                'Imdp2' => $password,
                'inscription' => "S'inscrire",
            ],
        ]);

        // Checking and Cleanup
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            0,
            $deletedRows,
            "Failed asserting that Password validation succeeded. Expected 0 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }

    public function test_it_prevents_passwords_that_do_not_match_confirmation(): void
    {
        $httpClient = TestKernelSingleton::get()->httpClient();
        $pdo = TestKernelSingleton::get()->pdo();

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

        // Checking and Cleanup
        $stmt = $pdo->prepare('DELETE FROM membres WHERE pseudo = :username');
        $stmt->execute([
            'username' => $username,
        ]);
        $deletedRows = $stmt->rowCount();

        $this->assertSame(
            0,
            $deletedRows,
            "Failed asserting that Password validation succeeded. Expected 0 newly signed up player to be deleted, but got {$deletedRows}.",
        );
    }
}
