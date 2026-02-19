<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(SignInPlayer::class)]
#[Small]
final class SignInPlayerTest extends TestCase
{
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
        string $username,
    ): void {
        $signInPlayer = new SignInPlayer($username);

        $this->assertSame($username, $signInPlayer->username);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     username: string,
     * }>
     */
    public static function requiredParametersProvider(): \Iterator
    {
        yield ['scenario' => 'username as a required parameter', 'username' => UsernameFixture::makeString()];
    }
}
