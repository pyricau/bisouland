<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Qa\Application\Action\InstantFreeUpgrade\InstantFreeUpgrade;
use Bl\Qa\Tests\Fixtures\Domain\Auth\Account\UsernameFixture;
use Bl\Qa\Tests\Fixtures\Domain\Game\Player\UpgradableLevels\UpgradableFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(InstantFreeUpgrade::class)]
#[Small]
final class InstantFreeUpgradeTest extends TestCase
{
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
        string $username,
        string $upgradable,
    ): void {
        $instantFreeUpgrade = new InstantFreeUpgrade($username, $upgradable);

        $this->assertSame($username, $instantFreeUpgrade->username);
        $this->assertSame($upgradable, $instantFreeUpgrade->upgradable);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     username: string,
     *     upgradable: string,
     * }>
     */
    public static function requiredParametersProvider(): \Iterator
    {
        yield ['scenario' => 'username as a required parameter', 'username' => UsernameFixture::makeString(), 'upgradable' => UpgradableFixture::makeString()];
        yield ['scenario' => 'upgradable as a required parameter', 'username' => UsernameFixture::makeString(), 'upgradable' => UpgradableFixture::makeString()];
    }

    #[DataProvider('optionalParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_optional_parameters(string $scenario, int $expectedLevels): void
    {
        $instantFreeUpgrade = new InstantFreeUpgrade(UsernameFixture::makeString(), UpgradableFixture::makeString());

        $this->assertSame($expectedLevels, $instantFreeUpgrade->levels);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     expectedLevels: int,
     * }>
     */
    public static function optionalParametersProvider(): \Iterator
    {
        yield ['scenario' => 'levels as an optional parameter (defaults to 1)', 'expectedLevels' => 1];
    }
}
