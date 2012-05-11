<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StatisticsController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $numberOfBirthsToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBirthsToday();
        
        $alivePopulationCount = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countAlivePopulation();
        $totalNumberOfBirth = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countTotalBirths();
        $deathCount = $totalNumberOfBirth - $alivePopulationCount;

        return compact(
                'numberOfBirthsToday',
                'alivePopulationCount',
                'totalNumberOfBirth',
                'deathCount'
        );
    }
}
