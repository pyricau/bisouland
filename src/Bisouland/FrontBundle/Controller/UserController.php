<?php

namespace Bisouland\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/user/register.html", name="registration")
     */
    public function registerAction(Request $request)
    {
        $locale = $request->getSession()->get('locale', 'en');
        $viewName = $this->makeViewName($locale, 'register');

        return $this->render($viewName);
    }

    /**
     * @param string $locale
     * @param string $action
     *
     * @return string
     */
    private function makeViewName($locale, $action)
    {
        $viewName = sprintf(
            'BisoulandFrontBundle:User:%s/%s.html.twig',
            $locale,
            $action
        );

        return $viewName;
    }
}
