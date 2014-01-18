<?php

namespace Bisouland\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redefinition of the confirmation route and success message.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class RegistrationController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction()
    {
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');

        $process = $formHandler->process();
        if (!$process) {
            return $this->container->get('templating')->renderResponse(
                'FOSUserBundle:Registration:register.html.twig',
                ['form' => $form->createView()]
            );
        }
        $user = $form->getData();

        $session = $this->container->get('session');
        $session->getFlashBag()->set('success', 'registration.flash.user_created');

        $redirectUrl = $this->container->get('router')->generate('home');
        $response = new RedirectResponse($redirectUrl);

        $this->authenticateUser($user, $response);

        return $response;
    }
}
