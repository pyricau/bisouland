<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;

class StatisticsController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $numberOfBeingsGeneratedToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBeingsGeneratedToday(); 

        $numberOfBeings = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->count();
        $totalNumberOfBeingsCreated = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->getLastId();
        $numberOfLosers = $totalNumberOfBeingsCreated - $numberOfBeings;
        $numberOfOthers = $numberOfBeings - $numberOfBeingsGeneratedToday;

        return compact(
                'numberOfBeingsGeneratedToday',
                'numberOfLosers',
                'numberOfOthers'
        );
    }
}
