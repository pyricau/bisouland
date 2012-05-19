<?php

namespace Bisouland\BeingsBundle\RandomSystem\Factory;

use PronounceableWord_Generator;
use Bisouland\BeingsBundle\RandomSystem\Character;

class CharacterFactory {
    static public $defaultNumberOfLifePoints = 8;

    static public $minimumNameLength = 4;
    static public $maximumNameLength = 9;
    
    static public $minimumDiceValue = 1;
    
    static public $attributeNumberOfDice = 6;
    static public $attributeDiceNumberOfFace = 6;
    static public $attributeNumberOfBestDiceResultToKeep = 3;

    private $nameGenerator;

    public function __construct(PronounceableWord_Generator $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }

    public function make()
    {
        $character = new Character();
        $character->name = $this->generateName();
        $character->attack = $this->drawAttribute();
        $character->defense = $this->drawAttribute();
        $character->constitution = $this->drawAttribute();
        $character->initialiseLifePoints();
        
        return $character;
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
}
