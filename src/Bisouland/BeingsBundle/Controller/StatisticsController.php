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
        $numberOfBirthsToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBeingsGeneratedToday(); 

        $alivePopulationCount = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countAlivePopulation();
        $totalNumberOfBirth = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countTotalBirths();
        $numberOfLosers = $totalNumberOfBirth - $alivePopulationCount;
        $numberOfOthers = $alivePopulationCount - $numberOfBirthsToday;

        return compact(
                'numberOfBirthsToday',
                'numberOfLosers',
                'numberOfOthers'
        );
    }
}
