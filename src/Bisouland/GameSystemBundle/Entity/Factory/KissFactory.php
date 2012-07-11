<?php

namespace Bisouland\GameSystemBundle\Entity\Factory;

use Doctrine\Common\Persistence\ManagerRegistry;

use Bisouland\GameSystemBundle\Kiss\Factory\SuccessFactory;
use Bisouland\GameSystemBundle\Kiss\Factory\DamagesFactory;
use Bisouland\GameSystemBundle\Entity\Lover;
use Bisouland\GameSystemBundle\Entity\Kiss;

use Bisouland\GameSystemBundle\Exception\InvalidLoverNameException;
use Bisouland\GameSystemBundle\Exception\InvalidSelfKissingException;
use Bisouland\GameSystemBundle\Exception\KissOverflowException;

class KissFactory
{
    public static $timeBetweenQuotaOfKiss = 43200;
    public static $quotaOfKiss = 3;

    private $doctrine;
    private $successFactory;
    private $damagesFactory;

    private $kisser;
    private $kissed;

    public function __construct(ManagerRegistry $doctrine, SuccessFactory $successFactory, DamagesFactory $damagesFactory)
    {
        $this->doctrine = $doctrine;
        $this->successFactory = $successFactory;
        $this->damagesFactory = $damagesFactory;
    }

    public function setKisserFromName($kisserName)
    {
        $this->kisser = $this->getLoverFromName($kisserName);

        return $this;
    }

    public function setKissedFromName($kissedName)
    {
        $this->kissed = $this->getLoverFromName($kissedName);

        return $this;
    }

    private function getLoverFromName($loverName)
    {
        $lover = $this->doctrine->getRepository('BisoulandGameSystemBundle:Lover')
                ->findOneByName($loverName);
        if (null === $lover) {
            throw new InvalidLoverNameException($loverName);
        }

        return $lover;
    }

    public function make()
    {
        $this->checkSelfKissing();
        $this->checkQuota();

        $success = $this->successFactory->make(
                $this->kisser->getSeductionBonus(),
                $this->kissed->getSlapBonus()
        );
        $this->damagesFactory->setKisser($this->kisser);
        $this->damagesFactory->setKissed($this->kissed);
        $this->damagesFactory->setSuccess($success);

        $kiss = new Kiss();
        $kiss->setkisser($this->kisser);
        $kiss->setkissed($this->kissed);
        $kiss->setIsCritical($success->getIsCritical());
        $kiss->setHasSucceeded($success->getIsSuccess());
        $kiss->setDamages($this->damagesFactory->make());

        return $kiss;
    }

    private function checkSelfKissing()
    {
        $kissedName = $this->kissed->getName();
        if ($this->kisser->getName() === $kissedName) {
            throw new InvalidSelfKissingException($kissedName);
        }
    }

    private function checkQuota()
    {
        $numberOfKiss = $this->doctrine->getRepository('BisoulandGameSystemBundle:Kiss')
                ->countForLastGivenSeconds(
                        $this->kisser->getId(),
                        $this->kissed->getId(),
                        self::$timeBetweenQuotaOfKiss
                );

        if (self::$quotaOfKiss <= $numberOfKiss) {
            throw new KissOverflowException(
                $this->kisser->getName()
                .','.$this->kissed->getName()
            );
        }
    }
}
