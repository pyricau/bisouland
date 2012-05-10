<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Filters\Before;
use Bisouland\BeingsBundle\Entity\Being;

/**
 * @Before("beforeFilter")
 */
class StatisticsController extends Controller
{
    static public $numberMaxOfBirthPerDay = 42;

    public function beforeFilter()
    {
        $this->birthGenerator();
    }

    protected function birthGenerator()
    {
        $numberOfBirthsToday = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBirthsToday();
        
        if (self::$numberMaxOfBirthPerDay > $numberOfBirthsToday) {
            $nameLength = mt_rand(4, 9);

            $nameGenerator = $this->container->get('pronounceable_word_generator');
            $randomName = ucfirst($nameGenerator->generateWordOfGivenLength($nameLength));

            $newBeing = new Being();
            $newBeing->setName($randomName);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($newBeing);
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
