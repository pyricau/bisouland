<?php

namespace Bisouland\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use LogicException;

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
        if ('POST' === $request->getMethod()) {
            $em = $this->getEntityManager();

            $user = $this->getUser();
            $entity = $em->getRepository('BisoulandUserBundle:User')->find($user->getId());

            $em->remove($entity);
            $em->flush();

            $this->get('session')
                ->getFlashBag()
                ->add('success', 'account.removal_confirmation.flash')
            ;

            return $this->redirect($this->generateUrl('fos_user_security_logout'));
        }

        return $this->render('BisoulandUserBundle:AccountRemoval:confirmation.html.twig');
    }

    /**
     * Shortcut to return the Entity Manager service.
     *
     * @return \Doctrine\ORM\EntityManager
     *
     * @throws LogicException If DoctrineBundle is not available
     */
    protected function getEntityManager()
    {
        if (!$this->container->has('doctrine.orm.default_entity_manager')) {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine.orm.default_entity_manager');
    }
}
