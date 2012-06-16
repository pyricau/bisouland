<?php

namespace Bisouland\GameSystemBundle\Entity\Factory;

use PronounceableWord_Generator;
use Bisouland\GameSystemBundle\Entity\Lover;

class LoverFactory {
    static public $minimumNameLength = 4;
    static public $maximumNameLength = 9;

    static public $minimumDiceValue = 1;

    static public $attributeNumberOfDice = 4;
    static public $attributeDiceNumberOfFace = 6;
    static public $attributeNumberOfBestDiceResultToKeep = 3;

    static public $defaultNumberOfLovePoints = 8;
    static public $lovePointMultiplierAsNumberOfSecondsInOneDay = 86400;

    private $nameGenerator;

    public function __construct(PronounceableWord_Generator $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }

    public function make()
    {
        $lover = new Lover();
        $lover->setName($this->generateName());
        $lover = $this->drawBonuses($lover);
        $lover->setLovePoints($this->initialiseLovePoints());

        return $lover;
    }

    private function generateName()
    {
        $nameLength = mt_rand(self::$minimumNameLength, self::$maximumNameLength);

        $randomName = $this->nameGenerator->generateWordOfGivenLength($nameLength);
        $randomName = ucfirst($randomName);

        return $randomName;
    }

    public function drawBonuses($lover)
    {
        $bonusMethods = array(
            'setSeductionBonus',
            'setDodgeBonus',
            'setSlapBonus',
            'setTongueBonus',
        );

        foreach ($bonusMethods as $bonusMethod) {
            $lover->{$bonusMethod}($this->generateBonus());
        }

        return $lover;
    }

    private function generateBonus()
    {
        $rollResults = $this->rollAttributeDice();
        $rollResults = $this->keepBestResults($rollResults);

        $attributePoints = array_sum($rollResults);
        $bonus = $this->getBonusFromGivenAttribute($attributePoints);
        
        return $bonus;
    }

    private function rollAttributeDice()
    {
        $rollResults = array();
        for ($numberOfRoll = 0; $numberOfRoll < self::$attributeNumberOfDice; $numberOfRoll++) {
            $rollResult = mt_rand(self::$minimumDiceValue, self::$attributeDiceNumberOfFace);
            array_push($rollResults, $rollResult);
        }
        
        return $rollResults;
    }

    private function keepBestResults($rollResults)
    {
        rsort($rollResults);
        $bestRollResults = array_slice($rollResults, 0, self::$attributeNumberOfBestDiceResultToKeep);
        
        return $bestRollResults;
    }

    private function getBonusFromGivenAttribute($attributePoints)
    {
        $mediumAttribute = 10;
        
        $bonus = floor(($attributePoints - $mediumAttribute) / 2);

        return intval($bonus);
    }

    private function initialiseLovePoints()
    {
        $lovePointBonus = $this->generateBonus();
        
        $lovePoints = self::$defaultNumberOfLovePoints + $lovePointBonus;

        return $lovePoints * self::$lovePointMultiplierAsNumberOfSecondsInOneDay;
    }
}
