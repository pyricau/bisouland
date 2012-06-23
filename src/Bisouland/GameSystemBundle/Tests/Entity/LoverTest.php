<?php

namespace Bisouland\GameSystemBundle\Tests\Entity;

use Bisouland\GameSystemBundle\Entity\Lover;

class LoverTest extends \PHPUnit_Framework_TestCase
{
    public function testNextLevelCost()
    {
        $levelsAndCosts = array(
            1 => 6,
            2 => 28,
            3 => 66,
            4 => 120,
            5 => 190,
        );

        foreach ($levelsAndCosts as $level => $expectedCost) {
            $lover = $this->makeLover(array('setLevel' => $level));
            $cost = $lover->getNextLevelCost() / Lover::$nextLevelCostMultiplier;

            $this->assertSame($expectedCost, $cost);
        }
    }

    public function testUpdatedLovePoints()
    {
        $secondsSinceLastUpdate = 42;
        $updated = new \DateTime("now - $secondsSinceLastUpdate seconds");

        $lovePoints = 1337;
        $expectedLovePoints = $lovePoints - $secondsSinceLastUpdate;

        $lover = $this->makeLover(array(
            'setLovePoints' => $lovePoints,
            'setUpdated' => $updated,
        ));

        $this->assertSame($expectedLovePoints, $lover->getLovePoints());
    }

    public function testAgeInSeconds()
    {
        $created = new \DateTime('yesterday');

        $lover = $this->makeLover(array(
            'setCreated' => $created,
        ));

        $this->assertSame(time() - $created->getTimestamp(), $lover->getAgeInSeconds());
    }

    private function makeLover(Array $methodsToSetValues)
    {
        $lover = new Lover();

        foreach ($methodsToSetValues as $method => $value) {
            $lover->{$method}($value);
        }

        return $lover;
    }
}
