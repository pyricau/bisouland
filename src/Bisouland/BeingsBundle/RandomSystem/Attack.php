<?php

namespace Bisouland\BeingsBundle\RandomSystem;

use Bisouland\BeingsBundle\RandomSystem\Character;

class Attack {
    private $attacker;
    private $defender;
    
    private $report;

    static public $minimumDiceValue = 1;
    static public $minimumDamagesValue = 1;
    static public $minimumRewardValue = 0;

    static public $hitDiceNumberOfFace = 20;
    static public $damagesDiceNumberOfFace = 4;

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
    
    public function damages()
    {
        $damagesRoll = mt_rand(self::$minimumDiceValue, self::$damagesDiceNumberOfFace);
        $attackerBonus = Character::calculateBonusPointsFromAttributePoints($this->attacker->attack);

        $this->report['defenderDamages'] = $damagesRoll + $attackerBonus;
        if ($this->report['defenderDamages'] < self::$minimumDamagesValue) {
            $this->report['defenderDamages'] = self::$minimumDamagesValue;
        }
    }
    
    public function reward()
    {
        $defenderBonus = Character::calculateBonusPointsFromAttributePoints($this->attacker->constitution);
        
        $this->report['attackerReward'] = $this->report['defenderDamages'] - $defenderBonus;
        if ($this->report['attackerReward'] < self::$minimumRewardValue) {
            $this->report['attackerReward'] = self::$minimumRewardValue;
        }
    }
}
