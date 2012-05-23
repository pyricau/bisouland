<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\RandomSystem\Character;

class StatisticsController extends Controller
{
    /**
     * @Template()
     */
    public function populationAction()
    {
        $numberOfBeingsGeneratedToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBeingsGeneratedToday(); 

        $numberOfBeings = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->count();
        $totalNumberOfBeingsCreated = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findLastId();
        $numberOfLosers = $totalNumberOfBeingsCreated - $numberOfBeings;
        $numberOfOthers = $numberOfBeings - $numberOfBeingsGeneratedToday;

        return compact(
                'numberOfBeingsGeneratedToday',
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
                ->getRepository('BisoulandBeingsBundle:Being')
                ->getAverageAttributes();

        $averageBonusSeduction = Character::calculateBonusPointsFromAttributePoints($averageAttributes[0][1]);
        $averageBonusSlap = Character::calculateBonusPointsFromAttributePoints($averageAttributes[0][2]);
        $averageBonusHeart = Character::calculateBonusPointsFromAttributePoints($averageAttributes[0][3]);

        return compact(
                'averageBonusSeduction',
                'averageBonusSlap',
                'averageBonusHeart'
        );
    }
}
