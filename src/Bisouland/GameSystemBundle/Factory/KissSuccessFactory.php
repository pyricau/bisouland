<?php

namespace Bisouland\GameSystemBundle\Factory;

use Bisouland\GameSystemBundle\Factory\RollFactory;
use Bisouland\GameSystemBundle\Entity\Lover;

class KissSuccessFactory
{
    static public $diceNumberOfSides = 20;
    static public $uninitialisedKisserRoll = -1;

    private $rollFactory;

    private $kisserRoll;

    public function __construct(RollFactory $rollFactory)
    {
        $this->rollFactory = $rollFactory;
        $this->kisserRoll = self::$uninitialisedKisserRoll;
    }

    public function getKisserRoll()
    {
        return $this->kisserRoll;
    }

    public function make($kisserBonus, $kissedBonus)
    {
        $this->rollFactory->setNumberOfSides(self::$diceNumberOfSides);

        $this->kisserRoll = $this->rollFactory->make();
        $kisserScore = $this->kisserRoll + $kisserBonus;

        $kissedRoll = $this->rollFactory->make();
        $kissedScore = $kissedRoll + $kissedBonus;

        return $kisserScore >= $kissedScore;
    }
}
