<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\BeingsBundle\Filters\Before;
use Bisouland\BeingsBundle\Entity\Being;

/**
 * @Before("beforeFilter")
 */
class DefaultController extends Controller
{
    public function beforeFilter()
    {
        $randomName = mt_rand();

        $newBeing = new Being();
        $newBeing->setName("$randomName");

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($newBeing);
        $em->flush();
    }

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $beings = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findAll();

        return array('beings' => $beings);
    }
}
