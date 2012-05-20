<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="beings")
     * @Template()
     */
    public function indexAction()
    {
        $beings = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findAll();

        return array('beings' => $beings);
    }
    
    /**
     * @Route("/amoureux/{name}", name="beings_view")
     * @Template()
     */
    public function viewAction($name)
    {
        $being = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($name);

        return array('being' => $being);
    }
}
