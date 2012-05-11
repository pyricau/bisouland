<?php

namespace Bisouland\BeingsBundle\Entity\Factory;

use Bisouland\BeingsBundle\Entity\Being;
use PronounceableWord_Generator;

class BeingFactory
{
    private $nameGenerator;
    
    static public $minimumNameLength = 4;
    static public $maximumNameLength = 9;

    public function __construct(PronounceableWord_Generator $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }
    
    public function make()
    {
        $being = new Being();
        $being->setName($this->generateName());
        
        return $being;
    }
    
    private function generateName()
    {
        $nameLength = mt_rand(self::$minimumNameLength, self::$maximumNameLength);

        $randomName = $this->nameGenerator->generateWordOfGivenLength($nameLength);
        $randomName = ucfirst($randomName);
        
        return $randomName;
    }
}
