<?php

declare(strict_types=1);

namespace Bl\Qa\Tests\Qalin\Spec\Application\Action;

use Bl\Auth\Tests\Fixtures\Account\UsernameFixture;
use Bl\Game\Tests\Fixtures\Player\UpgradableLevels\UpgradableFixture;
use Bl\Qa\Application\Action\UpgradeInstantlyForFree\UpgradeInstantlyForFree;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(UpgradeInstantlyForFree::class)]
#[Small]
final class UpgradeInstantlyForFreeTest extends TestCase
{
    #[DataProvider('requiredParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_required_parameters(
        string $scenario,
        string $username,
        string $upgradable,
    ): void {
        $upgradeInstantlyForFree = new UpgradeInstantlyForFree($username, $upgradable);

        $this->assertSame($username, $upgradeInstantlyForFree->username);
        $this->assertSame($upgradable, $upgradeInstantlyForFree->upgradable);
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
        yield [
            'scenario' => 'username as a required parameter',
            'username' => UsernameFixture::makeString(),
            'upgradable' => UpgradableFixture::makeString(),
        ];
        yield [
            'scenario' => 'upgradable as a required parameter',
            'username' => UsernameFixture::makeString(),
            'upgradable' => UpgradableFixture::makeString(),
        ];
    }

    #[DataProvider('optionalParametersProvider')]
    #[TestDox('It has $scenario')]
    public function test_it_has_optional_parameters(string $scenario, int $expectedLevels): void
    {
        $upgradeInstantlyForFree = new UpgradeInstantlyForFree(UsernameFixture::makeString(), UpgradableFixture::makeString());

        $this->assertSame($expectedLevels, $upgradeInstantlyForFree->levels);
    }

    /**
     * @return \Iterator<array{
     *     scenario: string,
     *     expectedLevels: int,
     * }>
     */
    public static function optionalParametersProvider(): \Iterator
    {
        yield [
            'scenario' => 'levels as an optional parameter (defaults to 1)',
            'expectedLevels' => 1,
        ];
    }
}
