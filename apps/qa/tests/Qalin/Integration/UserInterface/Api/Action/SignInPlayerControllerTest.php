<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Integration\UserInterface\Api\Action;

use Bl\Auth\Tests\Fixtures\Account\PasswordPlainFixture;
use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Qa\Application\Action\SignUpNewPlayer\SignUpNewPlayer;
use Bl\Qa\Tests\Monolith\Infrastructure\TestKernelSingleton;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversNothing]
#[Medium]
final class SignInPlayerControllerTest extends TestCase
{
    public function test_it_runs_action_successfully(): void
    {
        $username = UsernameFixture::makeString();
        TestKernelSingleton::get()->actionRunner()->run(
            new SignUpNewPlayer($username, PasswordPlainFixture::makeString()),
        );
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/actions/sign-in-player',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'username' => $username,
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode(), (string) $response->getContent());
    }

    /**
     * @param array<string, int|string> $body
     */
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
        array $body,
    ): void {
        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/actions/sign-in-player',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($body, \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode(), (string) $response->getContent());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     body: array<string, int|string>,
     * }>
     */
    public static function requiredParametersProvider(): \Iterator
    {
        yield [
            'scenario' => 'username as a required parameter',
            'body' => [],
        ];
    }

    /**
     * @param array<string, int|string> $body
     */
    #[DataProvider('invalidInputProvider')]
    #[TestDox('It fails on $scenario')]
    public function test_it_fails_on_invalid_input(
        string $scenario,
        array $body,
    ): void {
        if ('invalid username' !== $scenario) {
            TestKernelSingleton::get()->actionRunner()->run(
                new SignUpNewPlayer((string) $body['username'], PasswordPlainFixture::makeString()),
            );
        }

        $appKernel = TestKernelSingleton::get()->appKernel();

        $request = Request::create(
            uri: '/api/v1/actions/sign-in-player',
            method: 'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($body, \JSON_THROW_ON_ERROR),
        );

        $response = $appKernel->handle($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode(), (string) $response->getContent());
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     body: array<string, int|string>,
     * }>
     */
    public static function invalidInputProvider(): \Iterator
    {
        yield [
            'scenario' => 'invalid username',
            'body' => ['username' => 'x'],
        ];
    }
}
