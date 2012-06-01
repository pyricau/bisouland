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

            for ($attackAttribute = AttackFactoryTestCase::$minimumAttribute + 2; $attackAttribute < AttackFactoryTestCase::$maximumAttribute; $attackAttribute++) {
                $attacker->setAttack($attackAttribute);
                
                $minimumConstitution = 10;
                for ($constitutionAttribute = $minimumConstitution; $constitutionAttribute < AttackFactoryTestCase::$maximumAttribute; $constitutionAttribute++) {
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

    public function testEarningInferiorThanLoss()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();
        
        $defender->setDefense(AttackFactoryTestCase::$minimumAttribute);

        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult < $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attackAttribute = AttackFactoryTestCase::$minimumAttribute + 2; $attackAttribute < AttackFactoryTestCase::$maximumAttribute; $attackAttribute++) {
                $attacker->setAttack($attackAttribute);
                
                $maximumConstitution = 10;
                for ($constitutionAttribute = AttackFactoryTestCase::$minimumAttribute; $constitutionAttribute < $maximumConstitution; $constitutionAttribute++) {
                    $defender->setConstitution($constitutionAttribute);
                    $attack = $attackFactory->make($attacker, $defender);

                    $this->assertSame($attack->getAttackerEarning(), $attack->getDefenderLoss());
                }
            }
        }
    }
}
