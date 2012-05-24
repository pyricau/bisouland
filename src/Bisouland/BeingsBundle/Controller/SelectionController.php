<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;
use Bisouland\BeingsBundle\RandomSystem\Factory\CharacterFactory;

class SelectionController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        
        $hasGeneratedNewBeing = false;
        if (true === $this->hasToSelectBeing()) {
            $this->setBeingInSessionForNewVisitor();
        }
        $selectedBeing = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findOneByName($session->get(self::$sessionKeyForNnameOfBeingSelected));

        return compact(
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
    
    private function setBeingInSessionForNewVisitor()
    {
        $being = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findLastOne();

        $this->getRequest()
                ->getSession()
                ->set(self::$sessionKeyForNnameOfBeingSelected, $being->getName());
    }
    
    /**
     * @Route("/selection/{name}", name="beings_select")
     */
    public function selectionAction($name)
    {
        $selectedBeing = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findOneByName($name);
        
        if (null !== $selectedBeing) {
            $this->getRequest()
                    ->getSession()
                    ->set(self::$sessionKeyForNnameOfBeingSelected, $name);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }
}
