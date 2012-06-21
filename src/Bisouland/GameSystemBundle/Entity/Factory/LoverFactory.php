<?php

namespace Bisouland\GameSystemBundle\Entity\Factory;

use PronounceableWord_Generator;
use Bisouland\GameSystemBundle\Factory\BonusFactory;
use Bisouland\GameSystemBundle\Entity\Lover;

class LoverFactory
{
    static public $minimumNameLength = 4;
    static public $maximumNameLength = 9;

    static public $defaultNumberOfLovePoints = 8;
    static public $lovePointMultiplier = 86400;

    private $nameGenerator;
    private $bonusFactory;

    public function __construct(PronounceableWord_Generator $nameGenerator, BonusFactory $bonusFactory)
    {
        $this->nameGenerator = $nameGenerator;
        $this->bonusFactory = $bonusFactory;
    }

    public function make()
    {
        $lover = new Lover();
        $lover->setName($this->generateName());
        $lover->setSeductionBonus($this->bonusFactory->make());
        $lover->setDodgeBonus($this->bonusFactory->make());
        $lover->setSlapBonus($this->bonusFactory->make());
        $lover->setTongueBonus($this->bonusFactory->make());
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

    private function initialiseLovePoints()
    {
        $lovePointBonus = $this->bonusFactory->make();
        $lovePoints = self::$defaultNumberOfLovePoints + $lovePointBonus;

        return $lovePoints * self::$lovePointMultiplier;
    }
}
