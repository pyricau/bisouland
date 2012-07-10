<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;

class StatisticsController extends Controller
{
    /**
     * @Template()
     */
    public function populationAction()
    {
        $numberOfLoversGeneratedToday = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->countLoversGeneratedToday(); 

        $numberOfLovers = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->count();
        $numberOfLoversCreatedSinceTheBegining = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findLastId();
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
        $averageAttributes = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->getAverageAttributes();

        $averageBonusSeduction = Being::getBonusFromGivenAttribute($averageAttributes[0][1]);
        $averageBonusSlap = Being::getBonusFromGivenAttribute($averageAttributes[0][2]);
        $averageBonusHeart = Being::getBonusFromGivenAttribute($averageAttributes[0][3]);

        return compact(
                'averageBonusSeduction',
                'averageBonusSlap',
                'averageBonusHeart'
        );
    }
}
