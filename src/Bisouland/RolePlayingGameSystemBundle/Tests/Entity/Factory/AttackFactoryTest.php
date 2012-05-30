<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory\SimpleBeingFactory;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

class AttackFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $beingFactory;
    private $attackFactory;

    public function __construct()
    {
        $this->beingFactory = new SimpleBeingFactory();
        $this->attackFactory = new AttackFactory();
    }

    public function testDoesHit()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $attack = $this->attackFactory->make($attacker, $defender);

        $this->assertTrue($attack->getHasHit());
    }

    public function testDoesNotHit()
    {
        $attacker = $this->beingFactory->make();
        $defender = $this->beingFactory->make();

        $attack = $this->attackFactory->make($attacker, $defender);

        $this->assertFalse($attack->getHasHit());
    }
}
