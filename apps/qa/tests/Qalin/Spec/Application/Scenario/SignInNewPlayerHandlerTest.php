<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Scenario;

use Bl\Auth\Account\PasswordPlain;
use Bl\Auth\Account\Username;
use Bl\Auth\AuthToken;
use Bl\Auth\SaveAuthToken;
use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Game\FindPlayer;
use Bl\Game\SaveNewPlayer;
use Bl\Game\Tests\Fixtures\PlayerFixture;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayerHandler;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayerHandler;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignedInNewPlayer;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignInNewPlayer;
use Bl\Qa\Application\Scenario\SignInNewPlayer\SignInNewPlayerHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(SignInNewPlayerHandler::class)]
#[CoversClass(SignUpNewPlayerHandler::class)]
#[CoversClass(SignInPlayerHandler::class)]
#[Small]
final class SignInNewPlayerHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_signs_up_and_signs_in_a_new_player(): void
    {
        $username = UsernameFixture::makeString();
        $password = PasswordPlainFixture::makeString();
        $player = PlayerFixture::make();

        $saveNewPlayer = $this->prophesize(SaveNewPlayer::class);
        $saveNewPlayer->save(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
            Argument::that(static fn (PasswordPlain $p): bool => $p->toString() === $password),
        )->willReturn($player);

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::that(static fn (Username $u): bool => $u->toString() === $player->account->username->toString()),
        )->willReturn($player);

        $saveAuthToken = $this->prophesize(SaveAuthToken::class);
        $saveAuthToken->save(
            Argument::type(AuthToken::class),
        )->shouldBeCalled();

        $signInNewPlayerHandler = new SignInNewPlayerHandler(
            new SignUpNewPlayerHandler(
                $saveNewPlayer->reveal(),
            ),
            new SignInPlayerHandler(
                $findPlayer->reveal(),
                $saveAuthToken->reveal(),
            ),
        );
        $signedInNewPlayer = $signInNewPlayerHandler->run(new SignInNewPlayer(
            $username,
            $password,
        ));

        $this->assertInstanceOf(SignedInNewPlayer::class, $signedInNewPlayer);
        $this->assertSame($player, $signedInNewPlayer->signedUp->player);
    }

    /**
     * @param class-string<\Throwable> $exception
     */
    #[TestDox('It fails when $scenario')]
    #[DataProvider('failureProvider')]
    public function test_it_fails_when_saving_raises_an_error(
        string $scenario,
        string $exception,
    ): void {
        $username = UsernameFixture::makeString();
        $password = PasswordPlainFixture::makeString();

        $saveNewPlayer = $this->prophesize(SaveNewPlayer::class);
        $saveNewPlayer->save(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
            Argument::that(static fn (PasswordPlain $p): bool => $p->toString() === $password),
        )->willThrow($exception);

        $findPlayer = $this->prophesize(FindPlayer::class);
        $saveAuthToken = $this->prophesize(SaveAuthToken::class);

        $signInNewPlayerHandler = new SignInNewPlayerHandler(
            new SignUpNewPlayerHandler(
                $saveNewPlayer->reveal(),
            ),
            new SignInPlayerHandler(
                $findPlayer->reveal(),
                $saveAuthToken->reveal(),
            ),
        );

        $this->expectException($exception);
        $signInNewPlayerHandler->run(new SignInNewPlayer(
            $username,
            $password,
        ));
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      exception: class-string<\Throwable>,
     *  }>
     */
    public static function failureProvider(): \Iterator
    {
        yield [
            'scenario' => 'username is already registered',
            'exception' => ValidationFailedException::class,
        ];
        yield [
            'scenario' => 'an unexpected error occurs',
            'exception' => ServerErrorException::class,
        ];
    }

    public function test_it_fails_when_the_player_cannot_be_found_after_signing_up(): void
    {
        $username = UsernameFixture::makeString();
        $password = PasswordPlainFixture::makeString();
        $player = PlayerFixture::make();

        $saveNewPlayer = $this->prophesize(SaveNewPlayer::class);
        $saveNewPlayer->save(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
            Argument::that(static fn (PasswordPlain $p): bool => $p->toString() === $password),
        )->willReturn($player);

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::that(static fn (Username $u): bool => $u->toString() === $player->account->username->toString()),
        )->willThrow(ValidationFailedException::class);

        $saveAuthToken = $this->prophesize(SaveAuthToken::class);

        $signInNewPlayerHandler = new SignInNewPlayerHandler(
            new SignUpNewPlayerHandler(
                $saveNewPlayer->reveal(),
            ),
            new SignInPlayerHandler(
                $findPlayer->reveal(),
                $saveAuthToken->reveal(),
            ),
        );

        $this->expectException(ValidationFailedException::class);
        $signInNewPlayerHandler->run(new SignInNewPlayer(
            $username,
            $password,
        ));
    }

    public function test_it_fails_when_the_auth_token_cannot_be_saved(): void
    {
        $username = UsernameFixture::makeString();
        $password = PasswordPlainFixture::makeString();
        $player = PlayerFixture::make();

        $saveNewPlayer = $this->prophesize(SaveNewPlayer::class);
        $saveNewPlayer->save(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
            Argument::that(static fn (PasswordPlain $p): bool => $p->toString() === $password),
        )->willReturn($player);

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::that(static fn (Username $u): bool => $u->toString() === $player->account->username->toString()),
        )->willReturn($player);

        $saveAuthToken = $this->prophesize(SaveAuthToken::class);
        $saveAuthToken->save(
            Argument::type(AuthToken::class),
        )->willThrow(ServerErrorException::class);

        $signInNewPlayerHandler = new SignInNewPlayerHandler(
            new SignUpNewPlayerHandler(
                $saveNewPlayer->reveal(),
            ),
            new SignInPlayerHandler(
                $findPlayer->reveal(),
                $saveAuthToken->reveal(),
            ),
        );

        $this->expectException(ServerErrorException::class);
        $signInNewPlayerHandler->run(new SignInNewPlayer(
            $username,
            $password,
        ));
    }
}
