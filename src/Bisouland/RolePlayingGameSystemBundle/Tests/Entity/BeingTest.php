<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;

class BeingTest extends \PHPUnit_Framework_TestCase
{
    public function testBonusAttributes()
    {
        $attributesAndBonuses = array(
            3 => -4,
            4 => -3,
            5 => -3,
            6 => -2,
            7 => -2,
            8 => -1,
            9 => -1,
            10 => 0,
            11 => 0,
            12 => 1,
            13 => 1,
            14 => 2,
            15 => 2,
            16 => 3,
            17 => 3,
            18 => 4,
        );
        
        foreach ($attributesAndBonuses as $attribute => $expectedBonus) {
            $bonus = Being::getBonusFromGivenAttribute($attribute);

            $this->assertSame($bonus, $expectedBonus);
        }
    }
}
