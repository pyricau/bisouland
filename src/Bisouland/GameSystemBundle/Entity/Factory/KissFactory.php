<?php

namespace Bisouland\GameSystemBundle\Entity\Factory;

use Bisouland\GameSystemBundle\Entity\Factory\RollFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Entity\Kiss;

class KissFactory
{
    static public $minimumDiceValue = 1;
    static public $minimumLossValue = 1;
    static public $minimumEarningValue = 0;

    static public $criticalSuccessRoll = 20;
    static public $criticalFailRoll = 1;

    static public $successDiceNumberOfSides = 20;
    static public $damagesDiceNumberOfSides = 4;

    static public $damagesMultiplier = 3600;

    private $rollFactory;

    private $kisser;
    private $kissed;
    private $kiss;

    public function __construct(RollFactory $rollFactory)
    {
        $this->rollFactory = $rollFactory;
    }

    public function make(Lover $kisser, Lover $kissed)
    {
        $this->kisser = $kisser;
        $this->kissed = $kissed;

        $this->kiss = new Kiss();
        $this->kiss->setkisser($this->kisser);
        $this->kiss->setkissed($this->kissed);
        $this->kiss->setIsCritical(false);
        $this->kiss->setHasSucceeded(false);
        $this->kiss->setDamages(0);

        $this->success();
        $this->damages();

        return $this->kiss;
    }

    private function success()
    {
        $this->rollFactory->setNumberOfSidess(self::$successDiceNumberOfSides);

        $kisserRoll = $this->rollFactory->make();
        $kisserBonus = $this->kisser->getSeductionBonus();
        $kisserScore = $kisserRoll + $kisserBonus;

        $kissedRoll = $this->rollFactory->make();
        $kissedBonus = $this->kissed->getDodgeBonus();
        $kissedScore = $kissedRoll + $kissedBonus;

        $this->kiss->setHasSucceeded($kisserScore >= $kissedScore);
        $this->critical($kisserRoll);
    }

    private function critical($roll)
    {
        if (self::$criticalSuccessRoll === $roll) {
            $this->kiss->setIsCritical(true);
            $this->kiss->setHasSucceeded(true);
        }
        if (self::$criticalFailRoll === $roll) {
            $this->kiss->setIsCritical(true);
            $this->kiss->setHasSucceeded(false);
        }
    }

    private function damages($bonus)
    {
        if (true === $this->kiss->getHasSucceeded()) {
            $bonus = $this->kisser->getTongueBonus();
        } else {
            $bonus = $this->kissed->getSlapBonus();
        }

        $this->rollFactory->setNumberOfSidess(self::$damagesDiceNumberOfSides);

        $damagesRoll = $this->rollFactory->make();

        $damages = ($damagesRoll + $bonus) * self::$damagesMultiplier;
        if ($damages < self::$minimumDamagesValue) {
            $damages = self::$minimumDamagesValue * self::$damagesMultiplier;
        }

        if (true === $this->kiss->getHasSucceeded()) {
            $lovePoints = $this->kissed->getLovePoints();
        } else {
            $lovePoints = $this->kisser->getLovePoints();
        }

        if ($damages > $lifePoints) {
            $damages = $lifePoints;
        }

        $this->kiss->setDamages($damages);
    }
}
