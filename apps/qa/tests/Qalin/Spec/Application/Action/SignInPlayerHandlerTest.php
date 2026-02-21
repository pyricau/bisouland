<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Auth\Account\Username;
use Bl\Auth\AuthToken;
use Bl\Auth\SaveAuthToken;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Game\FindPlayer;
use Bl\Game\Tests\Fixtures\PlayerFixture;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayer;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayerHandler;
use Bl\Qa\Application\Action\SignInPlayer\SignInPlayerOutput;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(SignInPlayerHandler::class)]
#[Small]
final class SignInPlayerHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_runs_action_successfully(): void
    {
        $username = UsernameFixture::makeString();
        $player = PlayerFixture::make();

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
        )->willReturn($player);

        $saveAuthToken = $this->prophesize(SaveAuthToken::class);
        $saveAuthToken->save(
            Argument::type(AuthToken::class),
        )->shouldBeCalled();

        $signInPlayerHandler = new SignInPlayerHandler(
            $findPlayer->reveal(),
            $saveAuthToken->reveal(),
        );
        $signedInPlayer = $signInPlayerHandler->run(new SignInPlayer(
            $username,
        ));

        $this->assertInstanceOf(SignInPlayerOutput::class, $signedInPlayer);
    }

    public function test_it_fails_when_username_is_not_an_existing_one(): void
    {
        $username = UsernameFixture::makeString();

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
        )->willThrow(ValidationFailedException::class);

        $saveAuthToken = $this->prophesize(SaveAuthToken::class);

        $signInPlayerHandler = new SignInPlayerHandler(
            $findPlayer->reveal(),
            $saveAuthToken->reveal(),
        );

        $this->expectException(ValidationFailedException::class);
        $signInPlayerHandler->run(new SignInPlayer(
            $username,
        ));
    }

    public function test_it_fails_when_an_unexpected_error_occurs(): void
    {
        $username = UsernameFixture::makeString();
        $player = PlayerFixture::make();

        $findPlayer = $this->prophesize(FindPlayer::class);
        $findPlayer->find(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
        )->willReturn($player);

        $saveAuthToken = $this->prophesize(SaveAuthToken::class);
        $saveAuthToken->save(
            Argument::type(AuthToken::class),
        )->willThrow(ServerErrorException::class);

        $signInPlayerHandler = new SignInPlayerHandler(
            $findPlayer->reveal(),
            $saveAuthToken->reveal(),
        );

        $this->expectException(ServerErrorException::class);
        $signInPlayerHandler->run(new SignInPlayer(
            $username,
        ));
    }
}
