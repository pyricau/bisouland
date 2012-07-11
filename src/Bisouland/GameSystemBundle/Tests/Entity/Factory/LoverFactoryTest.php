<?php

namespace Bisouland\GameSystemBundle\Tests\Entity\Factory;

use Bisouland\GameSystemBundle\Tests\KernelAwareUnitTestCase;
use Bisouland\GameSystemBundle\Entity\Factory\LoverFactory;

class LoverFactoryTest extends KernelAwareUnitTestCase
{
    public function testLovePoints()
    {
        for ($bonusPoints = 4; $bonusPoints <= 4; $bonusPoints++) {
            $lover = $this->makeLoverFromGivenBonus($bonusPoints);

            $expectedLovePoints = LoverFactory::$defaultNumberOfLovePoints + $bonusPoints;
            $expectedLovePoints *= LoverFactory::$lovePointMultiplier;
            $expectedLovePoints -= (time() - $lover->getUpdated()->getTimestamp());

            $this->assertSame($expectedLovePoints, $lover->getLovePoints());
        }
    }

    private function makeLoverFromGivenBonus($bonus)
    {
        $bonusFactory = $this->getMockBuilder('Bisouland\GameSystemBundle\Factory\BonusFactory')
                ->disableOriginalConstructor()
                ->getMock();
 
        $bonusFactory->expects($this->any())
             ->method('make')
             ->will($this->returnValue($bonus));

        $loverFactory = new LoverFactory(
                $this->container->get('bisouland_pronounceable_word.generator'),
                $bonusFactory
        );
        $lover = $loverFactory->make();
        $lover->setUpdated(new \DateTime());

        return $lover;
    }
}
