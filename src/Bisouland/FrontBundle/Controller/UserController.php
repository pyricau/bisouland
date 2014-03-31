<?php

namespace Bisouland\FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/users/register.html")
     */
    public function registerAction(Request $request)
    {
        $locale = $request->getSession()->get('locale', 'en');
        $viewName = $this->makeViewName($locale, 'register');

        return $this->render($viewName);
    }

    /**
     * @Method({"GET"})
     * @Route("/users/list.html")
     */
    public function listAction(Request $request)
    {
        $locale = $request->getSession()->get('locale', 'en');
        $viewName = $this->makeViewName($locale, 'list');

        $userRepository = $this->getDoctrine()->getRepository('BisoulandApiBundle:User');

        $users = array();
        $doctrineUsers = $userRepository->findAll();
        foreach ($doctrineUsers as $doctrineUser) {
            $users[] = array(
                'username' => $doctrineUser->getUsername(),
                'love_points' => $doctrineUser->getLovePoints(),
            );
        }

        return $this->render($viewName, array(
            'users' => json_encode($users),
        ));
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
