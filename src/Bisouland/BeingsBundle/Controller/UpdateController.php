<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UpdateController extends Controller
{   
    /**
     * @Template()
     */
    public function indexAction()
    {
        $numberOfRemovedLosers = $this->removeLosers();
        
        return compact(
                'numberOfRemovedLosers'
        );
    }
    
    private function removeLosers()
    {
        $numberOfRemovedLosers = $this->getDoctrine()
                ->getRepository('BisoulandBeingsBundle:Being')
                ->removeLosers();
        
        return $numberOfRemovedLosers;
    }
}
