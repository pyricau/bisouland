<?php

namespace Bisouland\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Redefinition of the success message.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class ProfileController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('fos_user.profile.form');
        $formHandler = $this->container->get('fos_user.profile.form.handler');

        $process = $formHandler->process($user);
        if (!$process) {
            return $this->container->get('templating')->renderResponse(
                'FOSUserBundle:Profile:edit.html.twig',
                ['form' => $form->createView()]
            );
        }
        $session = $this->container->get('session');
        $session->getFlashBag()->set('success', 'profile.flash.updated');

        $router = $this->container->get('router');
        $redirectUrl = $router->generate('fos_user_profile_show');

        return new RedirectResponse($redirectUrl);
    }
}
