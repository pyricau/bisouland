<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory\SimpleBeingFactory;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\RollFactory;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

class AttackFactoryTest extends \PHPUnit_Framework_TestCase
{
    private static $minimumAttribute = 3;
    private static $maximumAttribute = 18;

    private $beingFactory;

    public function __construct()
    {
        $this->beingFactory = new SimpleBeingFactory();
    }

    private function getAttackFactoryWithRollsReturningGivenResult($result)
    {
        $rollFactory = $this->getMock('Bisouland\RolePlayingGameSystemBundle\Entity\Factory\RollFactory');
 
        $rollFactory->expects($this->any())
             ->method('make')
             ->will($this->returnValue($result));

        $attackFactory = new AttackFactory($rollFactory);

        return $attackFactory;
    }

    public function testHasHit()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();
        
        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult < $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attribute = self::$minimumAttribute; $attribute < self::$maximumAttribute; $attribute += 2) {
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

            for ($attribute = self::$minimumAttribute; $attribute < self::$maximumAttribute; $attribute += 2) {
                $attacker->setAttack($attribute);
                $defender->setDefense($attribute + 2);

                $attack = $attackFactory->make($attacker, $defender);

                $this->assertFalse($attack->getHasHit());
            }
        }
    }

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

    public function testLoss()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();
        
        $defender->setDefense(self::$minimumAttribute);

        $minimumDiceResult = AttackFactory::$criticalFail + 1;
        $maximumDiceResult = AttackFactory::$damagesDiceNumberOfFace;

        for ($diceResult = $minimumDiceResult; $diceResult < $maximumDiceResult; $diceResult++) {
            $attackFactory = $this->getAttackFactoryWithRollsReturningGivenResult($diceResult);

            for ($attribute = self::$minimumAttribute + 2; $attribute < self::$maximumAttribute; $attribute += 2) {
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
}
