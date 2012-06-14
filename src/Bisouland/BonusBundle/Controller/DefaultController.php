<?php

namespace Bisouland\BonusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\LoversBundle\Controller\SelectionController;
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

        $selectedLoverName = $this->getRequest()
                ->getSession()
                ->get(SelectionController::$sessionKey);
        $selectedLover = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findOneByName($selectedLoverName);
        
        if ($selectedLover->getLifePoints() >= $lovePointsToGet) {
            $answer = 'after';
            
            $numberOfBonuses = count($selectedLover->getBonuses());
            if (0 === $numberOfBonuses) {
                $answer = 'now';
                
                $bonus = new Bonus();
                $bonus->setLover($selectedLover);
                
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
