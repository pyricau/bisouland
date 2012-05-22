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
            'attackerName' => $this->attacker->name,
            'defenderName' => $this->defender->name,
            'isHitCritical' => false,
            'hasAttackerHit' => false,
            'defenderDamages' => 0,
            'attackerReward' => 0,
        );
    }
    
    public function make()
    {
        $this->hit();
        if (true === $this->report['hasAttackerHit']) {
            $this->damages();
            $this->reward();
        }
        
        return $this->report;
    }
    
    private function hit()
    {
        $attackerRoll = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        $attackerBonus = Character::calculateBonusPointsFromAttributePoints($this->attacker->attack);
        $attackerScore = $attackerRoll + $attackerBonus;
        
        $defenderRoll = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        $defenderBonus = Character::calculateBonusPointsFromAttributePoints($this->defender->defense);
        $defenderScore = $defenderRoll + $defenderBonus;
        
        $this->report['hasAttackerHit'] = $attackerScore > $defenderScore;
        $this->isHitCritical($attackerRoll);
    }
    
    private function isHitCritical($roll)
    {
        if (self::$hitDiceNumberOfFace === $roll) {
            $this->report['isHitCritical'] = true;
            $this->report['hasAttackerHit'] = true;
        }
        if (self::$minimumDiceValue === $roll) {
            $this->report['isHitCritical'] = true;
            $this->report['hasAttackerHit'] = false;
        }
    }
    
    private function damages()
    {
        $damagesRoll = mt_rand(self::$minimumDiceValue, self::$damagesDiceNumberOfFace);
        $attackerBonus = Character::calculateBonusPointsFromAttributePoints($this->attacker->attack);

        $this->report['defenderDamages'] = $damagesRoll + $attackerBonus;
        if ($this->report['defenderDamages'] < self::$minimumDamagesValue) {
            $this->report['defenderDamages'] = self::$minimumDamagesValue;
        }
    }
    
    private function reward()
    {
        $defenderBonus = Character::calculateBonusPointsFromAttributePoints($this->attacker->constitution);
        
        $this->report['attackerReward'] = $this->report['defenderDamages'] - $defenderBonus;
        if ($this->report['attackerReward'] < self::$minimumRewardValue) {
            $this->report['attackerReward'] = self::$minimumRewardValue;
        }
    }
}
