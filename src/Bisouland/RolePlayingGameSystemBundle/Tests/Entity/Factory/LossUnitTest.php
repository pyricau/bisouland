<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory\AttackFactoryTestCase;

use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

class LossUnitTest extends AttackFactoryTestCase
{
    public function testLoss()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $defender->setDefense(AttackFactoryTestCase::$minimumAttribute);

        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult <= $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attribute = AttackFactoryTestCase::$minimumAttribute + 2; $attribute < AttackFactoryTestCase::$maximumAttribute; $attribute += 2) {
                $attacker->setAttack($attribute);
                $attack = $attackFactory->make($attacker, $defender);

                $lossExpected = $diceResult + $attacker->getBonusAttack();
                if ($lossExpected < AttackFactory::$minimumLossValue) {
                    $lossExpected = AttackFactory::$minimumLossValue;
                }

                $this->assertSame($attack->getDefenderLoss(), $lossExpected);
            }
        }
    }

    public function testLossInferiorThanLifePoints()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $defender->setDefense(AttackFactoryTestCase::$minimumAttribute);

        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult <= $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attribute = AttackFactoryTestCase::$minimumAttribute + 2; $attribute < AttackFactoryTestCase::$maximumAttribute; $attribute += 2) {
                $attacker->setAttack($attribute);

                $minimumLifePoints = AttackFactory::$minimumLossValue + 1;
                $maximumLifePoints = $diceResult + $attacker->getBonusAttack();
                for ($lifePoints = $minimumLifePoints; $lifePoints < $maximumLifePoints; $lifePoints++) {
                    $defender->setLifePoints($lifePoints);
                    $attack = $attackFactory->make($attacker, $defender);

                    $this->assertSame($attack->getDefenderLoss(), $lifePoints);
                }
            }
        }
    }
}
