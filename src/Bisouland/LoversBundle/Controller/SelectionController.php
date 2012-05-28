<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SelectionController extends Controller
{
    public static $sessionKey = 'selectedLoverName';
    public static $flashKey = 'hasSelectedLover';

    /**
     * @Route("/selection/{name}", name="lovers_select")
     */
    public function selectionAction($name)
    {
        $selectedLover = $this->getDoctrine()
            ->getRepository('BisoulandLoversBundle:Lover')
            ->findOneByName($name);
        
        if (null !== $selectedLover) {
            $session = $this->getRequest()->getSession();
            $session->set(self::$sessionKey, $name);
            
            $session->setFlash(self::$flashKey, true);
        }

        return $this->redirect($this->generateUrl('lovers'));
    }
}
