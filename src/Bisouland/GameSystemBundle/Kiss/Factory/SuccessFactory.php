<?php

namespace Bisouland\GameSystemBundle\Kiss\Factory;

use Bisouland\GameSystemBundle\Factory\RollFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Kiss\Success;

class SuccessFactory
{
    static public $diceNumberOfSides = 20;

    static public $criticalSuccessRoll = 20;
    static public $criticalFailRoll = 1;

    private $rollFactory;

    private $isSuccess;
    private $isCritical;

    public function __construct(RollFactory $rollFactory)
    {
        $this->rollFactory = $rollFactory;
        $this->rollFactory->setNumberOfSides(self::$diceNumberOfSides);
    }

    public function make($kisserBonus, $kissedBonus)
    {
        $this->processSuccessAndCritical($kisserBonus, $kissedBonus);

        $success = new Success();
        $success->setIsSuccess($this->isSuccess);
        $success->setIsCritical($this->isCritical);
        
        return $success;
    }

    private function processSuccessAndCritical($kisserBonus, $kissedBonus)
    {
        $kisserRoll = $this->rollFactory->make();
        $kisserScore = $kisserRoll + $kisserBonus;

        $kissedRoll = $this->rollFactory->make();
        $kissedScore = $kissedRoll + $kissedBonus;

        $this->isSuccess = $kisserScore >= $kissedScore;

        $this->setCriticalFromGivenRoll($kisserRoll);
    }

    private function setCriticalFromGivenRoll($roll)
    {
        $this->isCritical = false;
        if (self::$criticalSuccessRoll === $roll) {
            $this->isCritical = true;
            $this->isSuccess = true;
        }
        if (self::$criticalFailRoll === $roll) {
            $this->isCritical = true;
            $this->isSuccess = false;
        }
    }
}
