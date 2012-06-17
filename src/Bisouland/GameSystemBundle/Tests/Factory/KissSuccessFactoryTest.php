<?php

namespace Bisouland\GameSystemBundle\Tests\Factory;

use Bisouland\GameSystemBundle\Factory\KissSuccessFactory;

class KissSuccessFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $kisserBonus;
    private $kissedBonus;
    private $kisserRoll;
    private $kissedRoll;

    private $kissSuccessFactory;

    public function testSuccess()
    {
        $minimumBonus = -4;
        $maximumBonus = 4;
        $minimumRoll = 1;
        $maximumRoll = KissSuccessFactory::$diceNumberOfSides;
        
        for ($this->kisserBonus = $minimumBonus; $this->kisserBonus <= $maximumBonus; $this->kisserBonus++) {
            for ($this->kissedBonus = $minimumBonus; $this->kissedBonus <= $maximumBonus; $this->kissedBonus++) {
                for ($this->kisserRoll = $minimumRoll; $this->kisserRoll <= $maximumRoll; $this->kisserRoll++) {
                    for ($this->kissedRoll = $minimumRoll; $this->kissedRoll <= $maximumRoll; $this->kissedRoll++) {
                        $this->makeKissSuccessFactory();

                        $this->checkCriticalFail();
                        $this->checkFail();
                        $this->checkSuccess();
                        $this->checkCriticalSuccess();
                    }
                }
            }
        }
    }

    private function checkCriticalFail()
    {
        if (KissSuccessFactory::$criticalFailRoll === $this->kisserRoll) {
            $this->assertTrue($this->kissSuccessFactory->isCritical());
            $this->assertFalse($this->kissSuccessFactory->isSuccess());
        }
    }

    private function checkFail()
    {
        if (KissSuccessFactory::$criticalFailRoll !== $this->kisserRoll
                and KissSuccessFactory::$criticalSuccessRoll !== $this->kisserRoll
                and ($this->kisserBonus + $this->kisserRoll < $this->kissedBonus + $this->kissedRoll)
        ) {
            $this->assertFalse($this->kissSuccessFactory->isCritical());
            $this->assertFalse($this->kissSuccessFactory->isSuccess());
        }
    }

    private function checkSuccess()
    {
        if (KissSuccessFactory::$criticalFailRoll !== $this->kisserRoll
                and KissSuccessFactory::$criticalSuccessRoll !== $this->kisserRoll
                and ($this->kisserBonus + $this->kisserRoll >= $this->kissedBonus + $this->kissedRoll)
        ) {
            $this->assertFalse($this->kissSuccessFactory->isCritical());
            $this->assertTrue($this->kissSuccessFactory->isSuccess());
        }
    }

    private function checkCriticalSuccess()
    {
        if (KissSuccessFactory::$criticalSuccessRoll === $this->kisserRoll) {
            $this->assertTrue($this->kissSuccessFactory->isCritical());
            $this->assertTrue($this->kissSuccessFactory->isSuccess());
        }
    }

    private function makeKissSuccessFactory()
    {
        $this->kissSuccessFactory = new KissSuccessFactory(
                $this->getRollFactoryForGivenTwoRolls($this->kisserRoll, $this->kissedRoll)
        );
        $this->kissSuccessFactory->make($this->kisserBonus, $this->kissedBonus);
    }

    private function getRollFactoryForGivenTwoRolls($kisserRoll, $kissedRoll)
    {
        $rollFactory = $this->getMock('Bisouland\GameSystemBundle\Factory\RollFactory');
 
        $rollFactory->expects($this->any())
                ->method('make')
                ->will($this->onConsecutiveCalls($kisserRoll, $kissedRoll));

        return $rollFactory;
    }
}
