<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StatisticsController extends Controller
{
    /**
     * @Template()
     */
    public function populationAction()
    {
        $numberOfLoversGeneratedToday = $this->getDoctrine()
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->countLoversGeneratedToday(); 

        $numberOfLovers = $this->getDoctrine()
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->count();
        $numberOfLoversCreatedSinceTheBegining = $this->getDoctrine()
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->findLastOne()
                ->getId();
        $numberOfLosers = $numberOfLoversCreatedSinceTheBegining - $numberOfLovers;
        $numberOfOthers = $numberOfLovers - $numberOfLoversGeneratedToday;

        return compact(
                'numberOfLoversGeneratedToday',
                'numberOfLosers',
                'numberOfOthers'
        );
    }

    /**
     * @Template()
     */
    public function bonusAction()
    {
        $bonusStatistics = $this->getDoctrine()
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->getBonusStatistics();

        return $bonusStatistics[0];
    }
}
