<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SelectionController extends Controller
{
    public static $sessionKey = 'selectedBeingName';
    public static $flashKey = 'hasSelectedBeing';

    /**
     * @Route("/selection/{name}", name="beings_select")
     */
    public function selectionAction($name)
    {
        $selectedBeing = $this->getDoctrine()
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findOneByName($name);
        
        if (null !== $selectedBeing) {
            $session = $this->getRequest()->getSession();
            $session->set(self::$sessionKey, $name);
            
            $session->setFlash(self::$flashKey, true);
        }

        return $this->redirect($this->generateUrl('beings'));
    }
}
