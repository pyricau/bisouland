<?php

namespace Bisouland\RolePlayingGameSystemBundle\Entity\Factory;

class RollFactory
{
    private $numberOfFaces;

    static public $minimumDiceValue = 1;
    static public $defaultNumberOfFace = 6;

    public function __construct()
    {
        $this->numberOfFaces = self::$defaultNumberOfFace;
    }

    public function setNumberOfFaces($numberOfFaces)
    {
        $this->numberOfFaces = $numberOfFaces;
    }

    public function make()
    {
        return mt_rand(self::$minimumDiceValue, $this->numberOfFaces);
    }
}
