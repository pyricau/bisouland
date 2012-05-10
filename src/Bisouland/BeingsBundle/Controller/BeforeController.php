<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Bisouland\BeingsBundle\Filters\Before;
use Bisouland\BeingsBundle\Entity\Being;

/**
 * @Before("beforeFilter")
 */
class BeforeController extends Controller
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
}
