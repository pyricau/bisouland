<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory\SimpleBeingFactory;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\RollFactory;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

class AttackFactoryTestCase extends \PHPUnit_Framework_TestCase
{
    public static $minimumAttribute = 3;
    public static $maximumAttribute = 18;

    protected $beingFactory;

    public function __construct()
    {
        $this->beingFactory = new SimpleBeingFactory();
    }

    protected function getAttackFactoryWithRollsReturningGivenResult($result)
    {
        $rollFactory = $this->getMock('Bisouland\RolePlayingGameSystemBundle\Entity\Factory\RollFactory');
 
        $rollFactory->expects($this->any())
             ->method('make')
             ->will($this->returnValue($result));

        $attackFactory = new AttackFactory($rollFactory);

        return $attackFactory;
    }
}
