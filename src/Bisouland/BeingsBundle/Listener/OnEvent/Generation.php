<?php

namespace Bisouland\BeingsBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;
use Symfony\Component\HttpFoundation\Session\Session;

use PDOException;

class Generation
{
    public static $quotaOfGeneration = 42;
    public static $sessionKey = 'hasGeneratedNewBeing';

    private $doctrine;
    private $beingFactory;
    private $session;

    public function __construct(ManagerRegistry $doctrine, BeingFactory $beingFactory, Session $session)
    {
        $this->doctrine = $doctrine;
        $this->beingFactory = $beingFactory;
        $this->session = $session;
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
        
        $this->session->setFlash(self::$sessionKey, $hasGeneratedNewBeing);
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
    
    private function flash()
    {
        $this->get('session')->setFlash('notice', 'Your changes were saved!');
    }
}
