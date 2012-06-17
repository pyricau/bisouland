<?php

namespace Bisouland\GameSystemBundle\Tests\Kiss\Factory;

use Bisouland\GameSystemBundle\Kiss\Factory\DamagesFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Kiss\Success;

class DamagesFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCriticalSuccess()
    {
        $roll = 2;
        $bonus = 2;
        $expectedDamages = ($roll + $bonus) * DamagesFactory::$damagesMultiplier;

        $damagesFactory = new DamagesFactory($this->makeRollFactory($roll));
        $damagesFactory->setKisser($this->makeLover(array(
            'setTongueBonus' => $bonus,
        )));
        $damagesFactory->setKissed($this->makeLover(array(
            'setLovePoints' => $expectedDamages * 2,
            'setUpdated' => new \DateTime(),
        )));
        $damagesFactory->setSuccess($this->makeSuccess(true, true));

        $this->assertSame($expectedDamages, $damagesFactory->make());
    }

    public function testCriticalFail()
    {
        $roll = 2;
        $bonus = 2;
        $expectedDamages = ($roll + $bonus) * DamagesFactory::$damagesMultiplier;

        $damagesFactory = new DamagesFactory($this->makeRollFactory($roll));
        $damagesFactory->setKisser($this->makeLover(array(
            'setLovePoints' => $expectedDamages * 2,
            'setUpdated' => new \DateTime(),
        )));
        $damagesFactory->setKissed($this->makeLover(array(
            'setSlapBonus' => $bonus,
        )));
        $damagesFactory->setSuccess($this->makeSuccess(false, true));

        $this->assertSame($expectedDamages, $damagesFactory->make());
    }

    public function testSuccess()
    {
        $roll = 2;
        $bonus = 2;
        $expectedDamages = ($roll + $bonus) * DamagesFactory::$damagesMultiplier;

        $damagesFactory = new DamagesFactory($this->makeRollFactory($roll));
        $damagesFactory->setKisser($this->makeLover(array(
            'setTongueBonus' => $bonus,
        )));
        $damagesFactory->setKissed($this->makeLover(array(
            'setLovePoints' => $expectedDamages * 2,
            'setUpdated' => new \DateTime(),
        )));
        $damagesFactory->setSuccess($this->makeSuccess(true, false));

        $this->assertSame($expectedDamages, $damagesFactory->make());
    }

    public function testFail()
    {
        $roll = 2;
        $bonus = 2;
        $expectedDamages = ($roll + $bonus) * DamagesFactory::$damagesMultiplier;

        $damagesFactory = new DamagesFactory($this->makeRollFactory($roll));
        $damagesFactory->setKisser($this->makeLover(array(
            'setLovePoints' => $expectedDamages * 2,
            'setUpdated' => new \DateTime(),
        )));
        $damagesFactory->setKissed($this->makeLover(array(
            'setSlapBonus' => $bonus,
        )));
        $damagesFactory->setSuccess($this->makeSuccess(false, false));

        $this->assertSame($expectedDamages, $damagesFactory->make());
    }

    public function testMinimumValue()
    {
        $roll = 1;
        $bonus = -4;
        $expectedDamages = DamagesFactory::$damagesMinimumValue * DamagesFactory::$damagesMultiplier;

        $damagesFactory = new DamagesFactory($this->makeRollFactory($roll));
        $damagesFactory->setKisser($this->makeLover(array(
            'setTongueBonus' => $bonus,
        )));
        $damagesFactory->setKissed($this->makeLover(array(
            'setLovePoints' => $expectedDamages * 2,
            'setUpdated' => new \DateTime(),
        )));
        $damagesFactory->setSuccess($this->makeSuccess(true, false));

        $this->assertSame($expectedDamages, $damagesFactory->make());
    }

    public function testNotEnoughLovePoints()
    {
        $roll = DamagesFactory::$diceNumberOfSides;
        $bonus = 4;
        $expectedDamages = ($roll + $bonus) * DamagesFactory::$damagesMultiplier;
        $expectedDamages /= 2;

        $damagesFactory = new DamagesFactory($this->makeRollFactory($roll));
        $damagesFactory->setKisser($this->makeLover(array(
            'setTongueBonus' => $bonus,
        )));
        $damagesFactory->setKissed($this->makeLover(array(
            'setLovePoints' => $expectedDamages,
            'setUpdated' => new \DateTime(),
        )));
        $damagesFactory->setSuccess($this->makeSuccess(true, false));

        $this->assertSame($expectedDamages, $damagesFactory->make());
    }

    private function makeRollFactory($roll)
    {
        $rollFactory = $this->getMock('Bisouland\GameSystemBundle\Factory\RollFactory');
 
        $rollFactory->expects($this->any())
                ->method('make')
                ->will($this->returnValue($roll));

        return $rollFactory;
    }

    private function makeLover($fields = array())
    {
        $lover = new Lover();

        foreach ($fields as $methodName => $fieldValue) {
            $lover->{$methodName}($fieldValue);
        }
        return $lover;
    }

    private function makeSuccess($isSuccess, $isCritical)
    {
        $success = new Success();
        $success->setIsSuccess($isSuccess);
        $success->setIsCritical($isCritical);

        return $success;
    }
}
