<?php

namespace Bisouland\RolePlayingGameSystemBundle\Tests\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\BeingFactory;

class SimpleBeingFactory {
    public function make()
    {
        $mediumAttributePoints = 10;

        $being = new Being();
        $being->setAttack($mediumAttributePoints);
        $being->setDefense($mediumAttributePoints);
        $being->setConstitution($mediumAttributePoints);
        $being->setLifePoints(BeingFactory::$defaultNumberOfLifePoints);

        return $being;
    }
}
