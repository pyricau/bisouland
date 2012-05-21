<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Controller\SelectionController;
use Bisouland\BeingsBundle\RandomSystem\Attack;
use Bisouland\BeingsBundle\RandomSystem\Character;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="beings")
     * @Template()
     */
    public function indexAction()
    {
        $beings = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findAll();

        return array('beings' => $beings);
    }
    
    /**
     * @Route("/{name}", name="beings_view")
     * @Template()
     */
    public function viewAction($name)
    {
        $being = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($name);

        return array('being' => $being);
    }
    
    /**
     * @Route("/embrasser/{name}", name="beings_attack")
     * @Template()
     */
    public function attackAction($name)
    {
        $numberOfSecondsInOneDay = 24 * 60 * 60;

        $attackerName = $this->getRequest()
                ->getSession()
                ->get(SelectionController::$sessionKeyForNnameOfBeingSelected);
        $attackerBeing = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($attackerName);

        $defenderBeing = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($name);
        
        $attackManager = new Attack(
                $this->beingToCharacter($attackerBeing),
                $this->beingToCharacter($defenderBeing)
        );
        $report = $attackManager->make();
        $report['defenderDamages'] *= $numberOfSecondsInOneDay;
        $report['attackerReward'] *= $numberOfSecondsInOneDay;
        
        return $report;
    }
    
    private function beingToCharacter($being)
    {
        $character = new Character();
        $character->name = $being->getName();
        $character->attack = $being->getSeduction();
        $character->defense = $being->getSlap();
        $character->constitution = $being->getHeart();
        $character->lifePoints = $being->getLovePoints();
        
        return $character;
    }
}
