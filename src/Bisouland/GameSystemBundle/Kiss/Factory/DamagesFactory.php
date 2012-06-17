<?php

namespace Bisouland\GameSystemBundle\Kiss\Factory;

use Bisouland\GameSystemBundle\Factory\RollFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Kiss\Success;

class DamagesFactory
{
    static public $diceNumberOfSides = 4;

    static public $damagesMinimumValue = 1;
    static public $damagesMultiplier = 3600;

    private $rollFactory;

    private $kisser;
    private $kissed;
    private $success;

    public function __construct(RollFactory $rollFactory)
    {
        $this->rollFactory = $rollFactory;
        $this->rollFactory->setNumberOfSides(self::$diceNumberOfSides);
    }

    public function setKisser(Lover $kisser)
    {
        $this->kisser = $kisser;
    }

    public function setKissed(Lover $kissed)
    {
        $this->kissed = $kissed;
    }

    public function setSuccess(Success $success)
    {
        $this->success = $success;
    }

    public function make()
    {
        $bonus = $this->getBonusFromWinner();
        $damagesRoll = $this->rollFactory->make();
        $damages = ($damagesRoll + $bonus) * self::$damagesMultiplier;

        $damages = $this->checkAgainstMinimumValue($damages);
        $damages = $this->checkAgainstMaximumValue($damages);

        return $damages;
    }

    private function getBonusFromWinner()
    {
        if (true === $this->success->getIsSuccess()) {
            $bonus = $this->kisser->getTongueBonus();
        } else {
            $bonus = $this->kissed->getSlapBonus();
        }
        
        return $bonus;
    }

    private function checkAgainstMinimumValue($damages)
    {
        if ($damages < self::$damagesMinimumValue) {
            $damages = self::$damagesMinimumValue * self::$damagesMultiplier;
        }

        return $damages;
    }

    private function checkAgainstMaximumValue($damages)
    {
        if (true === $this->success->getIsSuccess()) {
            $maximumDamages = $this->kissed->getLovePoints();
        } else {
            $maximumDamages = $this->kisser->getLovePoints();
        }

        if ($damages > $maximumDamages) {
            $damages = $maximumDamages;
        }

        return $damages;
    }
}
