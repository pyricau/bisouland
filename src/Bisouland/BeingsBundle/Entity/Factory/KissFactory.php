<?php

namespace Bisouland\BeingsBundle\Entity\Factory;

use Doctrine\Common\Persistence\ManagerRegistry;

use Bisouland\BeingsBundle\RandomSystem\Attack;
use Bisouland\BeingsBundle\Entity\Being;
use Bisouland\BeingsBundle\RandomSystem\Character;
use Bisouland\BeingsBundle\Entity\Kiss;

use Bisouland\BeingsBundle\Exception\InvalidKisserException;
use Bisouland\BeingsBundle\Exception\InvalidKissedException;
use Bisouland\BeingsBundle\Exception\InvalidKisserAsKissedException;
use Bisouland\BeingsBundle\Exception\OverflowKissException;

class KissFactory
{
    public static $quotaOfKiss = 3;
    public static $quotaOfSeconds = 43200;

    private $doctrine;
    
    private $kisser;
    private $kissed;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function make($kisserName, $kissedName)
    {
        $this->kisser = $this->doctrine->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($kisserName);
        $this->kissed = $this->doctrine->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($kissedName);
        
        $this->checkBeings();
        $this->checkTime();
        
        $kissReport = $this->getKissReport();
        
        $this->updateLovePoints($this->kisser, $kissReport['attackerEarning']);
        $this->updateLovePoints($this->kissed, -$kissReport['defenderLoss']);

        $kiss = $this->makeKissFromReport($kissReport);
        $this->saveKiss($kiss);
        
        return $kiss;
    }
    
    private function checkBeings()
    {
        if (null === $this->kisser) {
            throw new InvalidKisserException();
        }
        if (null === $this->kissed) {
            throw new InvalidKissedException();
        }
        if ($this->kisser->getName() === $this->kissed->getName()) {
            throw new InvalidKisserAsKissedException();
        }
    }
    
    private function checkTime()
    {
        $numberOfKiss = $this->doctrine->getRepository('BisoulandBeingsBundle:Kiss')
                ->countForLastGivenSeconds(
                        $this->kisser->getId(),
                        $this->kissed->getId(),
                        self::$quotaOfSeconds
                );
        
        if (self::$quotaOfKiss <= $numberOfKiss) {
            throw new OverflowKissException();
        }
    }
    
    private function getKissReport()
    {
        $attackManager = new Attack(
                $this->beingToCharacter($this->kisser),
                $this->beingToCharacter($this->kissed)
        );
        
        $numberOfSecondsInOneHour = 60 * 60;
        
        $report = $attackManager->make();
        $report['defenderLoss'] *= $numberOfSecondsInOneHour;
        $report['attackerEarning'] *= $numberOfSecondsInOneHour;
        
        return $report;
    }

    private function beingToCharacter(Being $being)
    {
        $character = new Character();
        $character->name = $being->getName();
        $character->attack = $being->getSeduction();
        $character->defense = $being->getSlap();
        $character->constitution = $being->getHeart();
        $character->lifePoints = $being->getLovePoints();
        
        return $character;
    }

    private function updateLovePoints(Being $being, $pointsToAdd)
    {
        $lovePoints = $being->getLovePoints() + $pointsToAdd;
        $being->setLovePoints($lovePoints);
        
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($being);
        $entityManager->flush();
    }

    private function makeKissFromReport($report)
    {
        $kiss = new Kiss();

        $kiss->setKisserEarning($report['attackerEarning']);
        $kiss->setKissedLoss($report['defenderLoss']);
        $kiss->setIsCritical($report['isHitCritical']);
        $kiss->setHasKissed($report['hasAttackerHit']);
        $kiss->setKisser($this->kisser);
        $kiss->setKissed($this->kissed);
        
        return $kiss;
    }

    private function saveKiss(Kiss $kiss)
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($kiss);
        $entityManager->flush();
    }
}
