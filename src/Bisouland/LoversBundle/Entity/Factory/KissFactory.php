<?php

namespace Bisouland\LoversBundle\Entity\Factory;

use Doctrine\Common\Persistence\ManagerRegistry;

use Bisouland\GameSystemBundle\Entity\Factory\KissFactory as GameSystemKissFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Entity\Kiss;

use Bisouland\LoversBundle\Exception\InvalidKisserException;
use Bisouland\LoversBundle\Exception\InvalidKissedException;
use Bisouland\LoversBundle\Exception\InvalidKisserAsKissedException;
use Bisouland\LoversBundle\Exception\KissOverflowException;

class KissFactory
{
    private $doctrine;
    private $kissFactory;

    private $kisser;
    private $kissed;

    public function __construct(ManagerRegistry $doctrine, GameSystemKissFactory $kissFactory)
    {
        $this->doctrine = $doctrine;
        $this->kissFactory = $kissFactory;
    }

    public function make($kisserName, $kissedName)
    {
        $kiss = $this->kissFactory
                ->setKisserFromName($kisserName)
                ->setKissedFromName($kissedName)
                ->make();

        $this->kisser = $kiss->getKisser();
        $this->kissed = $kiss->getKissed();

        $kisserDamages = $kiss->getDamages();
        $kissedDamages = -$kiss->getDamages();
        if (false === $kiss->getHasSucceeded()) {
            $kisserDamages *= -1;
            $kissedDamages *= -1;
        }
        $this->updateLovePoints($this->kisser, $kisserDamages);
        $this->updateLovePoints($this->kissed, $kissedDamages);

        $this->saveKiss($kiss);

        return $kiss;
    }

    private function updateLovePoints(Lover $lover, $pointsToAdd)
    {
        $lovePoints = $lover->getLovePoints() + $pointsToAdd;
        $lover->setLovePoints($lovePoints);

        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($lover);
        $entityManager->flush();
    }

    private function saveKiss(Kiss $kiss)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($kiss);
        $entityManager->flush();
    }
}
