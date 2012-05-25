<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SelectionController extends Controller
{   
    /**
     * @Route("/selection/{name}", name="beings_select")
     */
    public function selectionAction($name)
    {
        $selectedBeing = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findOneByName($name);
        
        if (null !== $selectedBeing) {
            $this->getRequest()
                    ->getSession()
                    ->set('selectedBeingName', $name);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }
}
