<?php

namespace Bisouland\GameSystemBundle\Factory;

use Bisouland\GameSystemBundle\Factory\RollFactory;
use Bisouland\GameSystemBundle\Entity\Lover;

class KissSuccessFactory
{
    static public $diceNumberOfSides = 20;

    static public $criticalSuccessRoll = 20;
    static public $criticalFailRoll = 1;

    private $rollFactory;

    private $success = false;
    private $critical = false;

    public function __construct(RollFactory $rollFactory)
    {
        $this->rollFactory = $rollFactory;
    }

    public function isSuccess()
    {
        return $this->success;
    }

    public function isCritical()
    {
        return $this->critical;
    }

    public function make($kisserBonus, $kissedBonus)
    {
        $this->rollFactory->setNumberOfSides(self::$diceNumberOfSides);

        $kisserRoll = $this->rollFactory->make();
        $kisserScore = $kisserRoll + $kisserBonus;

        $kissedRoll = $this->rollFactory->make();
        $kissedScore = $kissedRoll + $kissedBonus;

        $this->success = $kisserScore >= $kissedScore;

        $this->setCriticalFromGivenRoll($kisserRoll);
    }

    private function setCriticalFromGivenRoll($roll)
    {
        if (self::$criticalSuccessRoll === $roll) {
            $this->critical = true;
            $this->success = true;
        }
        if (self::$criticalFailRoll === $roll) {
            $this->critical = true;
            $this->success = false;
        }
    }
}
