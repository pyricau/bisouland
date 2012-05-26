<?php

namespace Bisouland\BonusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Controller\SelectionController;
use Bisouland\BonusBundle\Entity\Bonus;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="bonus")
     * @Template()
     */
    public function indexAction()
    {
        $lovePointsToGet = 42 * 24 * 60 * 60;
        $answer = 'before';

        $selectedBeingName = $this->getRequest()
                ->getSession()
                ->get(SelectionController::$sessionKey);
        $selectedBeing = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($selectedBeingName);
        
        if ($selectedBeing->getLovePoints() >= $lovePointsToGet) {
            $answer = 'after';
            
            $numberOfBonuses = count($selectedBeing->getBonuses());
            if (0 === $numberOfBonuses) {
                $answer = 'now';
                
                $bonus = new Bonus();
                $bonus->setBeing($selectedBeing);
                
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->persist($bonus);
                $entityManager->flush();
            }
        }

        return compact(
                'answer'
        );
    }
}
