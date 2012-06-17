<?php

namespace Bisouland\GameSystemBundle\Entity\Factory;

use Bisouland\GameSystemBundle\Factory\KissSuccessFactory;
use Bisouland\GameSystemBundle\Entity\Factory\RollFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Entity\Kiss;

class KissFactory
{
    static public $damagesMinimumValue = 1;
    static public $damagesDiceNumberOfSides = 4;
    static public $damagesMultiplier = 3600;

    private $kissSuccessFactory;
    private $rollFactory;

    private $kisser;
    private $kissed;
    private $kiss;

    public function __construct(KissSuccessFactory $kissSuccessFactory, RollFactory $rollFactory)
    {
        $this->kissSuccessFactory = $kissSuccessFactory;
        $this->rollFactory = $rollFactory;
    }

    public function make(Lover $kisser, Lover $kissed)
    {
        $this->kisser = $kisser;
        $this->kissed = $kissed;

        $this->kissSuccessFactory->make(
                $this->kisser->getSeductionBonus(),
                $this->kissed->getDodgeBonus()
        );

        $this->kiss = new Kiss();
        $this->kiss->setkisser($this->kisser);
        $this->kiss->setkissed($this->kissed);
        $this->kiss->setIsCritical($this->kissSuccessFactory->isCritical());
        $this->kiss->setHasSucceeded($this->kissSuccessFactory->isSuccess());
        $this->kiss->setDamages(0);

        $this->damages();

        return $this->kiss;
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
        if ($damages < self::$damagesMinimumValue) {
            $damages = self::$damagesMinimumValue * self::$damagesMultiplier;
        }

        if (true === $this->kiss->getHasSucceeded()) {
            $lovePoints = $this->kissed->getLovePoints();
        } else {
            $lovePoints = $this->kisser->getLovePoints();
        }

        if ($damages > $lovePoints) {
            $damages = $lovePoints;
        }

        $this->kiss->setDamages($damages);
    }
}
