<?php

namespace Bisouland\RolePlayingGameSystemBundle\Entity;

class Being {
    static public $defaultNumberOfLifePoints = 8;

    public $name;
    public $attack;
    public $defense;
    public $constitution;
    public $lifePoints;

    public function initialiseLifePoints()
    {
        $constitutionBonusPoint = $this->calculateBonusPointsFromAttributePoints($this->constitution);
        $this->lifePoints = self::$defaultNumberOfLifePoints + $constitutionBonusPoint;
    }

    static public function calculateBonusPointsFromAttributePoints($attributePoints)
    {
        return intval(($attributePoints - 10) / 2);
    }
}
