<?php

namespace Bisouland\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Confirmation page for account removal.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class AccountRemovalController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmationAction(Request $request)
    {
        $view = 'BisoulandUserBundle:AccountRemoval:confirmation.html.twig';
        if ('POST' !== $request->getMethod()) {
            return $this->container->get('templating')->renderResponse($view);
        }
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $token = $this->container->get('security.context')->getToken();
        if (null === $token) {
            return $this->container->get('templating')->renderResponse($view);
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            return $this->container->get('templating')->renderResponse($view);
        }

        $entity = $entityManager->getRepository('BisoulandUserBundle:User')
            ->find($user->getId())
        ;

        $entityManager->remove($entity);
        $entityManager->flush();

        $session = $this->container->get('session');
        $session->getFlashBag()->add('success', 'account.removal_confirmation.flash');

        return new RedirectResponse(
            $this->container->get('router')->generate('fos_user_security_logout')
        );
    }
}
