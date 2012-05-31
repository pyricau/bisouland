<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory\AttackFactoryTestCase;

use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

class EarningUnitTest extends AttackFactoryTestCase
{
    public function testEarning()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();
        
        $defender->setDefense(AttackFactoryTestCase::$minimumAttribute);

        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult < $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attackAttribute = AttackFactoryTestCase::$minimumAttribute + 2; $attackAttribute < AttackFactoryTestCase::$maximumAttribute; $attackAttribute += 2) {
                $attacker->setAttack($attackAttribute);
                for ($constitutionAttribute = AttackFactoryTestCase::$minimumAttribute; $constitutionAttribute < AttackFactoryTestCase::$maximumAttribute; $constitutionAttribute += 2) {
                    $defender->setConstitution($constitutionAttribute);
                    $attack = $attackFactory->make($attacker, $defender);

                    $earningExpected = $attack->getDefenderLoss() - $defender->getBonusConstitution();
                    if ($earningExpected < AttackFactory::$minimumEarningValue) {
                        $earningExpected = AttackFactory::$minimumEarningValue;
                    }

                    $this->assertSame($attack->getAttackerEarning(), $earningExpected);
                }
            }
        }
    }
}
