<?php

namespace Bisouland\GameSystemBundle\Factory;

use Bisouland\GameSystemBundle\Factory\AttributeFactory;

class BonusFactory
{
    private $attributeFactory;

    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    public function make()
    {
        $attributePoints = $this->attributeFactory->make();
        $bonusPoints = $this->getBonusFromGivenAttribute($attributePoints);

        return $bonusPoints;
    }

    public function getBonusFromGivenAttribute($attributePoints)
    {
        $mediumAttribute = 10;
        
        $bonus = floor(($attributePoints - $mediumAttribute) / 2);

        return intval($bonus);
    }
}
