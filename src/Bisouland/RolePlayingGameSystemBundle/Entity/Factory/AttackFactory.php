<?php

namespace Bisouland\RolePlayingGameSystemBundle\Entity\Factory;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;
use Bisouland\RolePlayingGameSystemBundle\Entity\Attack;

class AttackFactory {
    private $attacker;
    private $defender;

    static public $minimumDiceValue = 1;
    static public $minimumLossValue = 1;
    static public $minimumEarningValue = 0;

    static public $hitDiceNumberOfFace = 20;
    static public $damagesDiceNumberOfFace = 4;

    public function make(Being $attacker, Being $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;

        $this->attack = new Attack();
        $this->attack->setAttacker($this->attacker);
        $this->attack->setDefender($this->defender);
        $this->attack->setIsCritical(false);
        $this->attack->setHasHit(false);
        $this->attack->setDefenderLoss(0);
        $this->attack->setAttackerEarning(0);

        $this->hit();
        if (true === $this->attack->hasHit()) {
            $this->loss();
            $this->earning();
        }

        return $this->attack;
    }

    private function hit()
    {
        $attackerRoll = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        $attackerBonus = $this->attacker->getBonusAttack();
        $attackerScore = $attackerRoll + $attackerBonus;

        $defenderRoll = mt_rand(self::$minimumDiceValue, self::$hitDiceNumberOfFace);
        $defenderBonus = $this->defender->getBonusDefense();
        $defenderScore = $defenderRoll + $defenderBonus;

        $this->attack->setHasHit($attackerScore > $defenderScore);
        $this->critical($attackerRoll);
    }

    private function critical($roll)
    {
        if (self::$hitDiceNumberOfFace === $roll) {
            $this->attack->setIsCritical(true);
            $this->attack->setHasHit(true);
        }
        if (self::$minimumDiceValue === $roll) {
            $this->attack->setIsCritical(true);
            $this->attack->setHasHit(false);
        }
    }

    private function loss()
    {
        $damagesRoll = mt_rand(self::$minimumDiceValue, self::$damagesDiceNumberOfFace);
        $attackerBonus = $this->attacker->getBonusAttack();

        $defenderLoss = $damagesRoll + $attackerBonus;
        if ($defenderLoss < self::$minimumLossValue) {
            $defenderLoss = self::$minimumLossValue;
        }
        $this->attack->setDefenderLoss($defenderLoss);
    }

    private function earning()
    {
        $defenderBonus = $this->defender->getBonusConstitution();

        $attackerEarning = $this->attack->getDefenderLoss() + $defenderBonus;
        if ($attackerEarning < self::$minimumEarningValue) {
            $attackerEarning = self::$minimumEarningValue;
        }
        $this->attack->setAttackerEarning($attackerEarning);
    }
}
