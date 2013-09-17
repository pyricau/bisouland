<?php

namespace Bisouland\PreRegistrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * The homepage.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
