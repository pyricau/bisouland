<?php

namespace Bisouland\BeingsBundle\Entity\Factory;

use Bisouland\BeingsBundle\Entity\Being;
use Bisouland\BeingsBundle\RandomSystem\Factory\CharacterFactory;

class BeingFactory
{
    private $characterFactory;

    public function __construct(CharacterFactory $characterFactory)
    {
        $this->characterFactory = $characterFactory;
    }
    
    public function make()
    {
        $numberOfSecondsInOneDay = 24 * 60 * 60;

        $character = $this->characterFactory->make();

        $being = new Being();
        $being->setName($character->name);
        $being->setLovePoints($character->lifePoints * $numberOfSecondsInOneDay);
        $being->setSeduction($character->attack);
        $being->setSlap($character->defense);
        $being->setHeart($character->constitution);
        
        return $being;
    }
}
