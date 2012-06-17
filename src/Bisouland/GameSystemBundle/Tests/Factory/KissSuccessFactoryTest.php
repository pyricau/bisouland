<?php

namespace Bisouland\GameSystemBundle\Tests\Factory;

use Bisouland\GameSystemBundle\Factory\KissSuccessFactory;

class KissSuccessFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testHasSucceeded()
    {
        for ($kisserBonus = 3; $kisserBonus <= 4; $kisserBonus++) {
            for ($kissedBonus = -4; $kissedBonus < $kisserBonus; $kissedBonus++) {
                for ($kisserRoll = 2; $kisserRoll <= KissSuccessFactory::$diceNumberOfSides; $kisserRoll++) {
                    for ($kissedRoll = 1; $kissedRoll < $kisserRoll; $kissedRoll++) {
                        $kissSuccessFactory = new KissSuccessFactory(
                                $this->getRollFactoryForGivenTwoRolls($kisserRoll, $kissedRoll)
                        );

                        $this->assertTrue($kissSuccessFactory->make($kisserBonus, $kissedBonus));
                    }
                }
            }
        }
    }

    public function testHasNotSucceeded()
    {
        for ($kissedBonus = 3; $kissedBonus <= 4; $kissedBonus++) {
            for ($kisserBonus = -4; $kisserBonus < $kissedBonus; $kisserBonus++) {
                for ($kissedRoll = 2; $kissedRoll <= KissSuccessFactory::$diceNumberOfSides; $kissedRoll++) {
                    for ($kisserRoll = 1; $kisserRoll < $kissedRoll; $kisserRoll++) {
                        $kissSuccessFactory = new KissSuccessFactory(
                                $this->getRollFactoryForGivenTwoRolls($kisserRoll, $kissedRoll)
                        );

                        $this->assertFalse($kissSuccessFactory->make($kisserBonus, $kissedBonus));
                    }
                }
            }
        }
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
