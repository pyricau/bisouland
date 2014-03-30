<?php

namespace Bisouland\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RedirectController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/")
     */
    public function homepageAction(Request $request)
    {
        $locale = $request->getSession()->get('locale', 'en');
        $parameters = array(
            '_locale' => $locale,
            'file' => 'index.html',
        );
        $route = $this->generateUrl('page', $parameters);

        return $this->redirect($route);
    }
}
