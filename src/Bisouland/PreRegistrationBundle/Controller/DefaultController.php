<?php

namespace Bisouland\PreRegistrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Loic Chardonnet <loic.chardonnet@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @Route(
     *     pattern = "/",
     *     name = "home"
     * )
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
