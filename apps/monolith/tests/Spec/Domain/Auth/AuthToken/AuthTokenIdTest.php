<?php

declare(strict_types=1);

namespace Bl\Tests\Spec\Domain\Auth\AuthToken;

use Bl\Domain\Auth\AuthToken\AuthTokenId;
use Bl\Exception\ValidationFailedException;
use Bl\Tests\Spec\Domain\Auth\AuthToken\Fixtures\AuthTokenIdFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuthTokenId::class)]
#[Small]
final class AuthTokenIdTest extends TestCase
{
    #[TestDox('It can be converted from/to string')]
    public function test_it_can_be_converted_from_and_to_string(): void
    {
        $stringAuthTokenId = AuthTokenIdFixture::makeString();
        $authTokenId = AuthTokenId::fromString($stringAuthTokenId);

        $this->assertInstanceOf(AuthTokenId::class, $authTokenId);
        $this->assertSame($stringAuthTokenId, $authTokenId->toString());
    }

    #[TestDox('It generates unique IDs')]
    public function test_it_generates_unique_ids(): void
    {
        $authTokenId1 = AuthTokenId::create();
        $authTokenId2 = AuthTokenId::create();

        // If fromString doesn't throw, the format is valid
        AuthTokenId::fromString($authTokenId1->toString());
        AuthTokenId::fromString($authTokenId2->toString());

        $this->assertInstanceOf(AuthTokenId::class, $authTokenId1);
        $this->assertInstanceOf(AuthTokenId::class, $authTokenId2);
        $this->assertNotSame($authTokenId1->toString(), $authTokenId2->toString());
    }

    #[TestDox('It fails when it is not a valid UUID')]
    public function test_it_fails_when_it_is_not_a_valid_uuid(): void
    {
        $this->expectException(ValidationFailedException::class);

        AuthTokenId::fromString('not-a-valid-uuid');
    }
}
