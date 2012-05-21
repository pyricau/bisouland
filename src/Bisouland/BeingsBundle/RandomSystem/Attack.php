<?php

namespace Bisouland\BeingsBundle\RandomSystem;

use Bisouland\BeingsBundle\RandomSystem\Character;

class Attack {
    private $attacker;
    private $defender;
    
    private $report;

    static public $minimumDiceValue = 1;

    static public $hitDiceNumberOfFace = 20;

    public function __construct(Character $attacker, Character $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;
        
        $this->report = array(
            'attackerRoll' => 0,
            'defenderRoll' => 0,
            'hasAttackerHit' => false,
            'defenderDamages' => 0,
            'attackerReward' => 0,
        );
    }
    
    public function hit()
    {
        $this->report['attackerRoll'] = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        $attackerBonus = Character::calculateBonusPointsFromAttributePoints($this->attacker->attack);
        $attackerScore = $this->report['attackerRoll'] + $attackerBonus;
        
        $this->report['defenderRoll'] = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        $defenderBonus = Character::calculateBonusPointsFromAttributePoints($this->defender->defense);
        $defenderScore = $this->report['defenderRoll'] + $defenderBonus;
        
        $this->report['hasAttackerHit'] = $attackerScore > $defenderScore;
    }
    
    public function criticalHit()
    {
        if (self::$hitDiceNumberOfFace === $this->report['attackerRoll']) {
            $this->report['hasAttackerHit'] = true;
        }

        if (self::$minimumDiceValue === $this->report['attackerRoll']) {
            $this->report['hasAttackerHit'] = false;
        }
    }
}
