<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;
use Bisouland\BeingsBundle\RandomSystem\Factory\CharacterFactory;

class UpdateController extends Controller
{
    static public $maximumNumberOfBirthInOneDay = 42;
    
    /**
     * @Template()
     */
    public function indexAction()
    {
        $numberOfGeneratedBirth = $this->generateBirth();
        $numberOfRemovedLosers = $this->removeLosers();
        
        return compact(
                'numberOfGeneratedBirth',
                'numberOfRemovedLosers'
        );
    }
    
    private function generateBirth()
    {
        $numberOfGeneratedBirth = 0;
        $numberOfBirthsToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBirthsToday();
        
        if (self::$maximumNumberOfBirthInOneDay > $numberOfBirthsToday) {
            $beingFactory = new BeingFactory(new CharacterFactory($this->get('pronounceable_word_generator')));

            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->persist($beingFactory->make());
            $entityManager->flush();
            $numberOfGeneratedBirth = 1;
        }
        
        return $numberOfGeneratedBirth;
    }
    
    private function removeLosers()
    {
        $numberOfPopulationBefore = $numberOfBirthsToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countAlivePopulation();

        $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->removeLosers();
        
        $numberOfPopulationAfter = $numberOfBirthsToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countAlivePopulation();

        $numberOfRemovedLosers = $numberOfPopulationBefore - $numberOfPopulationAfter;
        
        return $numberOfRemovedLosers;
    }
}
