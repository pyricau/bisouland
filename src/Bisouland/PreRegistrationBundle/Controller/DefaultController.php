<?php

namespace Bisouland\PreRegistrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * The homepage.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('BisoulandPreRegistrationBundle:Default:index.html.twig');
    }
}
