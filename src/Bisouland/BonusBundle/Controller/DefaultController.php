<?php

namespace Bisouland\BonusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Controller\SelectionController;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="bonus")
     * @Template()
     */
    public function indexAction()
    {
        $lovePointsToGet = 42 * 42 * 60 * 60;
        $answer = 'not-enough';

        $selectedBeingName = $this->getRequest()
                ->getSession()
                ->get(SelectionController::$sessionKey);
        $selectedBeing = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($selectedBeingName);
        
        if ($selectedBeing->getLovePoints() >= $lovePointsToGet) {
            $answer = 'success';
            
        }
        return array();
    }
}
