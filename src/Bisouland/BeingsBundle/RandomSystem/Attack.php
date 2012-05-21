<?php

namespace Bisouland\BeingsBundle\RandomSystem;

use Bisouland\BeingsBundle\RandomSystem\Character;

class Attack {
    private $attacker;
    private $defender;
    
    static public $minimumDiceValue = 1;
    
    static public $hitDiceNumberOfFace = 20;
    
    
    public function __construct(Character $attacker, Character $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;
    }
    
    public function hit()
    {
        $attackerRoll = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        $defenderRoll = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        
        $attackerScore = $attackerRoll + Character::calculateBonusPointsFromAttributePoints($this->attacker->attack);
        $defenderScore = $defenderRoll + Character::calculateBonusPointsFromAttributePoints($this->defender->defense);
        
        return $attackerScore > $defenderScore;
    }
}
