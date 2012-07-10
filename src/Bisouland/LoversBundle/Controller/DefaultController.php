<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="lovers")
     * @Template()
     */
    public function indexAction()
    {
        $lovers = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findAll();

        return array('lovers' => $lovers);
    }
    
    /**
     * @Route("/{name}", name="lovers_view")
     * @Template()
     */
    public function viewAction($name)
    {
        $lover = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findOneByName($name);

        return array('lover' => $lover);
    }
}
