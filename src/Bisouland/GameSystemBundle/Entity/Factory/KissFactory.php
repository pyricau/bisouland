<?php

namespace Bisouland\GameSystemBundle\Entity\Factory;

use Bisouland\GameSystemBundle\Kiss\Factory\SuccessFactory;
use Bisouland\GameSystemBundle\Kiss\Factory\DamagesFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Entity\Kiss;

class KissFactory
{
    private $successFactory;
    private $damagesFactory;

    public function __construct(SuccessFactory $successFactory, DamagesFactory $damagesFactory)
    {
        $this->successFactory = $successFactory;
        $this->damagesFactory = $damagesFactory;
    }

    public function make(Lover $kisser, Lover $kissed)
    {
        $success = $this->successFactory->make(
                $kisser->getSeductionBonus(),
                $kissed->getDodgeBonus()
        );
        $this->damagesFactory->setKisser($kisser);
        $this->damagesFactory->setKissed($kissed);
        $this->damagesFactory->setSuccess($success);

        $kiss = new Kiss();
        $kiss->setkisser($kisser);
        $kiss->setkissed($kissed);
        $kiss->setIsCritical($success->getIsCritical());
        $kiss->setHasSucceeded($success->getIsSuccess());
        $kiss->setDamages($this->damagesFactory->make());

        return $kiss;
    }
}
