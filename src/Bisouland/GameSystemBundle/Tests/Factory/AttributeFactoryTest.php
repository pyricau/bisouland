<?php

namespace Bisouland\GameSystemBundle\Tests\Factory;

use Bisouland\GameSystemBundle\Factory\AttributeFactory;

class AttributeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBonusAttributes()
    {
        $attributeAndrollResults = array(
            3 => array(1, 1, 1, 1),
            4 => array(1, 1, 1, 2),
            5 => array(2, 1, 1, 2),
            6 => array(1, 2, 3, 1),
            7 => array(1, 2, 3, 2),
            8 => array(1, 2, 3, 3),
            9 => array(2, 3, 4, 1),
            10 => array(3, 5, 1, 2),
            11 => array(6, 2, 2, 3),
            12 => array(4, 4, 4, 3),
            13 => array(4, 5, 4, 4),
            14 => array(4, 5, 3, 5),
            15 => array(5, 5, 5, 4),
            16 => array(6, 5, 5, 5),
            17 => array(5, 6, 6, 5),
            18 => array(6, 6, 6, 6),
        );

        foreach ($attributeAndrollResults as $expectedAttribute => $rollResults) {
            $attribute = $this->makeAttributeFromGivenRollResults($rollResults);

            $this->assertSame($expectedAttribute, $attribute);
        }
    }

    private function makeAttributeFromGivenRollResults($rollResults)
    {
        $rollFactory = $this->getMock('Bisouland\GameSystemBundle\Factory\RollFactory');
 
        $rollFactory->expects($this->any())
                ->method('make')
                ->will($this->onConsecutiveCalls(
                        $rollResults[0],
                        $rollResults[1],
                        $rollResults[2],
                        $rollResults[3]
                ));

        $attributeFactory = new AttributeFactory($rollFactory);

        return $attributeFactory->make();
    }
}
