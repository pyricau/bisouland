<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Filters\Before;
use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;

/**
 * @Before("beforeFilter")
 */
class StatisticsController extends Controller
{
    static public $maximumNumberOfBirthInOneDay = 42;

    public function beforeFilter()
    {
        $this->generateBirth();
    }
    
    private function generateBirth()
    {
        $numberOfBirthsToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBirthsToday();
        
        if (self::$maximumNumberOfBirthInOneDay > $numberOfBirthsToday) {
            $beingFactory = new BeingFactory($this->get('pronounceable_word_generator'));

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($beingFactory->make());
            $em->flush();
        }
    }

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
