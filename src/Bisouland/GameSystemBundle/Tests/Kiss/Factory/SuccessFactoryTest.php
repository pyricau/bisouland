<?php

namespace Bisouland\GameSystemBundle\Tests\Kiss\Factory;

use Bisouland\GameSystemBundle\Kiss\Factory\SuccessFactory;

class SuccessFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $kisserBonus;
    private $kissedBonus;
    private $kisserRoll;
    private $kissedRoll;

    private $successFactory;
    private $succes;

    public function testSuccess()
    {
        $minimumBonus = -4;
        $maximumBonus = 4;
        $minimumRoll = 1;
        $maximumRoll = SuccessFactory::$diceNumberOfSides;
        
        for ($this->kisserBonus = $minimumBonus; $this->kisserBonus <= $maximumBonus; $this->kisserBonus++) {
            for ($this->kissedBonus = $minimumBonus; $this->kissedBonus <= $maximumBonus; $this->kissedBonus++) {
                for ($this->kisserRoll = $minimumRoll; $this->kisserRoll <= $maximumRoll; $this->kisserRoll++) {
                    for ($this->kissedRoll = $minimumRoll; $this->kissedRoll <= $maximumRoll; $this->kissedRoll++) {
                        $this->makeSuccess();

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
        if (SuccessFactory::$criticalFailRoll === $this->kisserRoll) {
            $this->assertTrue($this->success->getIsCritical());
            $this->assertFalse($this->success->getIsSuccess());
        }
    }

    private function checkFail()
    {
        if (SuccessFactory::$criticalFailRoll !== $this->kisserRoll
                and SuccessFactory::$criticalSuccessRoll !== $this->kisserRoll
                and ($this->kisserBonus + $this->kisserRoll < $this->kissedBonus + $this->kissedRoll)
        ) {
            $this->assertFalse($this->success->getIsCritical());
            $this->assertFalse($this->success->getIsSuccess());
        }
    }

    private function checkSuccess()
    {
        if (SuccessFactory::$criticalFailRoll !== $this->kisserRoll
                and SuccessFactory::$criticalSuccessRoll !== $this->kisserRoll
                and ($this->kisserBonus + $this->kisserRoll >= $this->kissedBonus + $this->kissedRoll)
        ) {
            $this->assertFalse($this->success->getIsCritical());
            $this->assertTrue($this->success->getIsSuccess());
        }
    }

    private function checkCriticalSuccess()
    {
        if (SuccessFactory::$criticalSuccessRoll === $this->kisserRoll) {
            $this->assertTrue($this->success->getIsCritical());
            $this->assertTrue($this->success->getIsSuccess());
        }
    }

    private function makeSuccess()
    {
        $this->successFactory = new SuccessFactory(
                $this->getRollFactoryForGivenTwoRolls($this->kisserRoll, $this->kissedRoll)
        );
        $this->success = $this->successFactory->make($this->kisserBonus, $this->kissedBonus);
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
