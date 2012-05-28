<?php

namespace Bisouland\LoversBundle\Entity\Factory;

use Bisouland\LoversBundle\Entity\Lover;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\BeingFactory;

class LoverFactory
{
    private $beingFactory;

    public function __construct(BeingFactory $beingFactory)
    {
        $this->beingFactory = $beingFactory;
    }

    public function make()
    {
        $numberOfSecondsInOneDay = 24 * 60 * 60;

        $being = $this->beingFactory->make();

        $lover = new Lover();
        $lover->setName($being->getName());
        $lover->setLifePoints($being->getLifePoints() * $numberOfSecondsInOneDay);
        $lover->setAttack($being->getAttack());
        $lover->setDefense($being->getDefense());
        $lover->setConstitution($being->getConstitution());

        return $lover;
    }
}
