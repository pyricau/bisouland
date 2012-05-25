<?php

namespace Bisouland\BeingsBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;
use Symfony\Component\HttpFoundation\Session\Session;

use PDOException;

class Generation
{
    public static $quotaOfGeneration = 42;
    public static $flashKey = 'hasGeneratedNewBeing';

    private $doctrine;
    private $session;
    private $beingFactory;

    public function __construct(ManagerRegistry $doctrine, Session $session, BeingFactory $beingFactory)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->beingFactory = $beingFactory;
    }
    
    public function make()
    {
        $hasGeneratedNewBeing = false;
        if (true === $this->hasToGenerateNewBeing()) {
            $hasGeneratedNewBeing = true;
            try {
                $this->generateNewBeing();
            } catch (PDOException $e) {
                $hasGeneratedNewBeing = false;
            }
        }
        
        $this->session->setFlash(self::$flashKey, $hasGeneratedNewBeing);
    }
    
    private function hasToGenerateNewBeing()
    {
        $numberOfBeings = $this->doctrine
                ->getRepository('BisoulandBeingsBundle:Being')
                ->count();
        $isNumberOfBeingsUnderQuota = self::$quotaOfGeneration > $numberOfBeings;

        $numberOfBeingsGeneratedToday = $this->doctrine
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBeingsGeneratedToday();
        $isGenerationNumberUnderQuota = self::$quotaOfGeneration > $numberOfBeingsGeneratedToday;
        
        $hasToGenerateNewBeing = false;
        if ($isNumberOfBeingsUnderQuota || $isGenerationNumberUnderQuota) {
            $hasToGenerateNewBeing = true;
        }
        
        return $hasToGenerateNewBeing;
    }
    
    private function generateNewBeing()
    {
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($this->beingFactory->make());
        $entityManager->flush();
    }
}
