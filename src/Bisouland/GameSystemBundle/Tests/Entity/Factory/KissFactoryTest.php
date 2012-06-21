<?php

namespace Bisouland\GameSystemBundle\Tests\Entity\Factory;

use Bisouland\GameSystemBundle\Tests\KernelAwareUnitTestCase;

use Bisouland\GameSystemBundle\Entity\Factory\KissFactory;
use Bisouland\GameSystemBundle\Exception\InvalidLoverNameException;
use Bisouland\GameSystemBundle\Exception\InvalidSelfKissingException;

class KissFactoryTest extends KernelAwareUnitTestCase
{
    public function testInvalidKisser()
    {
        $kissFactory = $this->makeKissFactory();

        $isInvalid = false;
        try {
            $kissFactory->setKisserFromName('Non-existing name');
        }
        catch (InvalidLoverNameException $e) {
            $isInvalid = true;
        }

        $this->assertTrue($isInvalid);
    }

    public function testInvalidKissed()
    {
        $kissFactory = $this->makeKissFactory();

        $isInvalid = false;
        try {
            $kissFactory->setKissedFromName('Non-existing name');
        }
        catch (InvalidLoverNameException $e) {
            $isInvalid = true;
        }

        $this->assertTrue($isInvalid);
    }

    public function testInvalidSelfKissing()
    {
        $kissFactory = $this->makeKissFactory();

        $isInvalid = false;
        try {
            $kissFactory
                ->setKisserFromName('TestLoverForSelfKissing')
                ->setKissedFromName('TestLoverForSelfKissing')
                ->make();
        }
        catch (InvalidSelfKissingException $e) {
            $isInvalid = true;
        }

        $this->assertTrue($isInvalid);
    }

    private function makeKissFactory()
    {
        $successFactory = $this->getMockBuilder('Bisouland\GameSystemBundle\Kiss\Factory\SuccessFactory')
                ->disableOriginalConstructor()
                ->getMock();
 
        $damagesFactory = $this->getMockBuilder('Bisouland\GameSystemBundle\Kiss\Factory\DamagesFactory')
                ->disableOriginalConstructor()
                ->getMock();

        $kissFactory = new KissFactory(
                $this->container->get('doctrine'),
                $successFactory,
                $damagesFactory
        );

        return $kissFactory;
    }
}
