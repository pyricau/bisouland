<?php

namespace Bisouland\LoversBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

use Bisouland\GameSystemBundle\Entity\Factory\LoverFactory;

class Generation
{
    public static $quotaOfGeneration = 42;
    public static $flashKey = 'hasGeneratedNewLover';

    private $doctrine;
    private $session;
    private $loverFactory;

    public function __construct(ManagerRegistry $doctrine, Session $session, LoverFactory $loverFactory)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->loverFactory = $loverFactory;
    }

    public function make()
    {
        $hasGeneratedNewLover = false;
        if (true === $this->hasToGenerateNewLover()) {
            $hasGeneratedNewLover = true;
            try {
                $this->generateNewLover();
            } catch (\PDOException $e) {
                $hasGeneratedNewLover = false;
            }
        }

        $this->session->setFlash(self::$flashKey, $hasGeneratedNewLover);
    }

    private function hasToGenerateNewLover()
    {
        $numberOfLovers = $this->doctrine
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->count();
        $isNumberOfLoversUnderQuota = self::$quotaOfGeneration > $numberOfLovers;

        $numberOfLoversGeneratedToday = $this->doctrine
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->countLoversGeneratedToday();
        $isGenerationNumberUnderQuota = self::$quotaOfGeneration > $numberOfLoversGeneratedToday;

        $hasToGenerateNewLover = false;
        if ($isNumberOfLoversUnderQuota || $isGenerationNumberUnderQuota) {
            $hasToGenerateNewLover = true;
        }

        return $hasToGenerateNewLover;
    }

    private function generateNewLover()
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($this->loverFactory->make());
        $entityManager->flush();
    }
}
