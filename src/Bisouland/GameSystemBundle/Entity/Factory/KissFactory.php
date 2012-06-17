<?php

namespace Bisouland\GameSystemBundle\Entity\Factory;

use Bisouland\GameSystemBundle\Kiss\Factory\SuccessFactory;
use Bisouland\GameSystemBundle\Entity\Factory\RollFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Entity\Kiss;

class KissFactory
{
    static public $damagesMinimumValue = 1;
    static public $damagesDiceNumberOfSides = 4;
    static public $damagesMultiplier = 3600;

    private $SuccessFactory;
    private $rollFactory;

    private $kisser;
    private $kissed;
    private $kiss;

    public function __construct(SuccessFactory $SuccessFactory, RollFactory $rollFactory)
    {
        $this->SuccessFactory = $SuccessFactory;
        $this->rollFactory = $rollFactory;
    }

    public function make(Lover $kisser, Lover $kissed)
    {
        $this->kisser = $kisser;
        $this->kissed = $kissed;

        $success = $this->SuccessFactory->make(
                $this->kisser->getSeductionBonus(),
                $this->kissed->getDodgeBonus()
        );

        $this->kiss = new Kiss();
        $this->kiss->setkisser($this->kisser);
        $this->kiss->setkissed($this->kissed);
        $this->kiss->setIsCritical($success->getIsCritical());
        $this->kiss->setHasSucceeded($success->getIsSuccess());
        $this->kiss->setDamages(0);

        $this->damages();

        return $this->kiss;
    }
}
