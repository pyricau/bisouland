<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;
use Bisouland\BeingsBundle\RandomSystem\Factory\CharacterFactory;

class SelectionController extends Controller
{
    static public $maximumNumberOfBeingGenerationInOneDay = 42;
    static public $sessionKeyForNnameOfBeingSelected = 'nameOfSelectedBeing';

    /**
     * @Template()
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        
        $hasGeneratedNewBeing = false;
        if (true === $this->hasToSelectBeing()) {
            $hasGeneratedNewBeing = $this->tryGenerateNewBeing();
            $this->setBeingInSessionForNewVisitor();
        }
        $selectedBeing = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findOneByName($session->get(self::$sessionKeyForNnameOfBeingSelected));

        return compact(
                'hasGeneratedNewBeing',
                'selectedBeing'
        );
    }
    
    private function hasToSelectBeing()
    {
        $session = $this->getRequest()->getSession();

        $hasSelectedBeing = $session->has(self::$sessionKeyForNnameOfBeingSelected);
        if (false === $hasSelectedBeing) {
            return true;
        }
        $selectedBeing = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findOneByName($session->get(self::$sessionKeyForNnameOfBeingSelected));
        
        return (null === $selectedBeing);
    }
    
    private function tryGenerateNewBeing()
    {
        $hasGeneratedNewBeing = false;

        $numberOfBeingsGeneratedToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBeingsGeneratedToday();
        
        if (self::$maximumNumberOfBeingGenerationInOneDay > $numberOfBeingsGeneratedToday) {
            $this->generateNewBeing();
            $hasGeneratedNewBeing = true;
        }
        
        return $hasGeneratedNewBeing;
    }
    
    private function generateNewBeing()
    {
        $beingFactory = new BeingFactory(new CharacterFactory($this->get('pronounceable_word_generator')));

        $entityManager = $this->getDoctrine()->getEntityManager();
        $entityManager->persist($beingFactory->make());
        $entityManager->flush();
    }
    
    private function setBeingInSessionForNewVisitor()
    {
        $being = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findLastOne();

        $this->getRequest()
                ->getSession()
                ->set(self::$sessionKeyForNnameOfBeingSelected, $being->getName());
    }
}
