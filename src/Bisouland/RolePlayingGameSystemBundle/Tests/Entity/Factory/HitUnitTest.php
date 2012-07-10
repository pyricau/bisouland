<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory\AttackFactoryTestCase;

use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

class HitUnitTest extends AttackFactoryTestCase
{
    public function testHasHit()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();
        
        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult < $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attribute = AttackFactoryTestCase::$minimumAttribute; $attribute < AttackFactoryTestCase::$maximumAttribute; $attribute += 2) {
                $defender->setDefense($attribute);
                $attacker->setAttack($attribute + 2);

                $attack = $attackFactory->make($attacker, $defender);

                $this->assertTrue($attack->getHasHit());
            }
        }
    }

    public function testHasNotHit()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult < $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attribute = AttackFactoryTestCase::$minimumAttribute; $attribute < AttackFactoryTestCase::$maximumAttribute; $attribute += 2) {
                $attacker->setAttack($attribute);
                $defender->setDefense($attribute + 2);

                $attack = $attackFactory->make($attacker, $defender);

                $this->assertFalse($attack->getHasHit());
            }
        }
    }
}
