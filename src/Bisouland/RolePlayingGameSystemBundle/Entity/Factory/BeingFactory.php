<?php

namespace Bisouland\RolePlayingGameSystemBundle\Entity\Factory;

use PronounceableWord_Generator;
use Bisouland\RolePlayingGameSystemBundle\Entity\Being;

class BeingFactory {
    static public $defaultNumberOfLifePoints = 8;

    static public $minimumNameLength = 4;
    static public $maximumNameLength = 9;

    static public $minimumDiceValue = 1;

    static public $attributeNumberOfDice = 4;
    static public $attributeDiceNumberOfFace = 6;
    static public $attributeNumberOfBestDiceResultToKeep = 3;

    private $nameGenerator;

    public function __construct(PronounceableWord_Generator $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }

    public function make()
    {
        $being = new Being();
        $being->setName($this->generateName());
        $being->setAttack($this->drawAttribute());
        $being->setDefense($this->drawAttribute());
        $being->setConstitution($this->drawAttribute());
        $being->setLifePoints($this->initialiseLifePoints($being->getBonusConstitution()));

        return $being;
    }

    private function generateName()
    {
        $nameLength = mt_rand(self::$minimumNameLength, self::$maximumNameLength);

        $randomName = $this->nameGenerator->generateWordOfGivenLength($nameLength);
        $randomName = ucfirst($randomName);

        return $randomName;
    }

    private function drawAttribute()
    {
        $rollResults = array();
        for ($numberOfRoll = 0; $numberOfRoll < self::$attributeNumberOfDice; $numberOfRoll++) {
            $rollResult = mt_rand(self::$minimumDiceValue, self::$attributeDiceNumberOfFace);
            array_push($rollResults, $rollResult);
        }
        rsort($rollResults);
        $bestRollResults = array_slice($rollResults, 0, self::$attributeNumberOfBestDiceResultToKeep);

        $attributePoints = array_sum($bestRollResults);

        return $attributePoints;
    }

    private function initialiseLifePoints($bonusConstitution)
    {
        return self::$defaultNumberOfLifePoints + $bonusConstitution;
    }
}
