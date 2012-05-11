<?php

namespace Bisouland\BeingsBundle\Entity\Factory;

use Bisouland\BeingsBundle\Entity\Being;
use PronounceableWord_Generator;

class BeingFactory
{
    private $nameGenerator;
    
    static public $minimumNameLength = 4;
    static public $maximumNameLength = 9;
    
    static public $minimumLovePointsInDays = 4;
    static public $maximumLovePointsInDays = 10;

    public function __construct(PronounceableWord_Generator $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }
    
    public function make()
    {
        $being = new Being();
        $being->setName($this->generateName());
        $being->setLovePoints($this->generateLovePoints());
        
        return $being;
    }
    
    private function generateName()
    {
        $nameLength = mt_rand(self::$minimumNameLength, self::$maximumNameLength);

        $randomName = $this->nameGenerator->generateWordOfGivenLength($nameLength);
        $randomName = ucfirst($randomName);
        
        return $randomName;
    }
    
    private function generateLovePoints()
    {
        $numberOfSecondsInOneDay = 24 * 60 * 60;
        $minimumLovePointsInSeconds = self::$minimumLovePointsInDays * $numberOfSecondsInOneDay;
        $maximumLovePointsInSeconds = self::$maximumLovePointsInDays * $numberOfSecondsInOneDay;
        
        $love_points = mt_rand($minimumLovePointsInSeconds, $maximumLovePointsInSeconds);
        
        return $love_points;
    }
}
