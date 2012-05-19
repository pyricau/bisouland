<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{   
    private function updateLovePoints()
    {
        $entityManager = $this->getDoctrine()->getEntityManager();
        $beings = $entityManager->getRepository('BisoulandBeingsBundle:Being')->findAll();
        foreach ($beings as $being) {
            $being->setLovePoints($being->getLovePoints());
        }
        $entityManager->flush();
    }

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
