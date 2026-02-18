<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Auth\Account\PasswordPlain;
use Bl\Auth\Account\Username;
use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Exception\ServerErrorException;
use Bl\Exception\ValidationFailedException;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayerHandler;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayerOutput;
use Bl\Qa\Domain\Game\SaveNewPlayer;
use Bl\Qa\Tests\Fixtures\Domain\Game\PlayerFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(SignUpNewPlayerHandler::class)]
#[Small]
final class SignUpNewPlayerHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_signs_up_a_new_player_for_a_given_username_and_password(): void
    {
        $username = UsernameFixture::makeString();
        $password = PasswordPlainFixture::makeString();
        $expectedPlayer = PlayerFixture::make();

        $saveNewPlayer = $this->prophesize(SaveNewPlayer::class);

        $saveNewPlayer->save(
            Argument::that(static fn (Username $u): bool => $u->toString() === $username),
            Argument::that(static fn (PasswordPlain $p): bool => $p->toString() === $password),
        )->willReturn($expectedPlayer);

        $signUpNewPlayerHandler = new SignUpNewPlayerHandler(
            $saveNewPlayer->reveal(),
        );
        $output = $signUpNewPlayerHandler->run(new SignUpNewPlayer($username, $password));

        $this->assertInstanceOf(SignUpNewPlayerOutput::class, $output);
        $this->assertSame($expectedPlayer, $output->player);
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

        $signUpNewPlayerHandler = new SignUpNewPlayerHandler(
            $saveNewPlayer->reveal(),
        );

        $this->expectException($exception);
        $signUpNewPlayerHandler->run(new SignUpNewPlayer($username, $password));
    }

    /**
     * @return \Iterator<array{
     *      scenario: string,
     *      exception: class-string<\Throwable>,
     *  }>
     */
    public static function failureProvider(): \Iterator
    {
        yield ['scenario' => 'Username is already registered', 'exception' => ValidationFailedException::class];
        yield ['scenario' => 'CloudCoordinates X/Y are already occupied (race condition)', 'exception' => ValidationFailedException::class];
        yield ['scenario' => 'CloudCoordinates Y is not available (cloud is full)', 'exception' => ValidationFailedException::class];
        yield ['scenario' => 'an unexpected error occurs (database or remote endpoint failure)', 'exception' => ServerErrorException::class];
    }
}
