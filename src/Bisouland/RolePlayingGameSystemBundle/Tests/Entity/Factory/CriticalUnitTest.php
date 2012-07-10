<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory\AttackFactoryTestCase;

use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

class CriticalUnitTest extends AttackFactoryTestCase
{
    public function testIsNotCritical()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$criticalHit;

        for ($diceResult = $minimumDiceResult; $diceResult < $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);
            $attack = $attackFactory->make($attacker, $defender);

            $this->assertFalse($attack->getIsCritical());
        }
    }

    public function testIsCriticalHit()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult(AttackFactory::$criticalHit);
        $attack = $attackFactory->make($attacker, $defender);

        $this->assertTrue($attack->getIsCritical());
        $this->assertTrue($attack->getHasHit());
    }

    public function testIsCriticalFail()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult(AttackFactory::$criticalFail);
        $attack = $attackFactory->make($attacker, $defender);

        $this->assertTrue($attack->getIsCritical());
        $this->assertFalse($attack->getHasHit());
    }
}
