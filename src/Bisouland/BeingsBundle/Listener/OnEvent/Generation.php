<?php

namespace Bisouland\BeingsBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;

class Generation
{
    public static $quotaOfGeneration = 42;

    private $doctrine;
    private $beingFactory;

    public function __construct(ManagerRegistry $doctrine, BeingFactory $beingFactory)
    {
        $this->doctrine = $doctrine;
        $this->beingFactory = $beingFactory;
    }
    
    public function make()
    {
        if (true === $this->hasToGenerateNewBeing()) {
            $this->generateNewBeing();
        }
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
