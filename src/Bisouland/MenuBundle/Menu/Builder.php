<?php

namespace Bisouland\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\SecurityContext;

/**
 * Builds the application menus.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class Builder
{
    /**
     * @var FactoryInterface
     */
    private $menuFactory;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @param FactoryInterface $menuFactory
     * @param SecurityContext  $securityContext
     */
    public function __construct(FactoryInterface $menuFactory, SecurityContext $securityContext)
    {
        $this->menuFactory = $menuFactory;
        $this->securityContext = $securityContext;
    }

    /**
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createLoginMenu(Request $request)
    {
        $menu = $this->menuFactory->createItem('loginMenu');
        $menu->setChildrenAttribute('class', 'menu');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->addChild('menu.logged_out.login', array('route' => 'fos_user_security_login'));
        $menu->addChild('menu.logged_out.register', array('route' => 'fos_user_registration_register'));

        return $menu;
    }

    /**
     * @param Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createUserMenu(Request $request)
    {
        $menu = $this->menuFactory->createItem('userMenu');
        $menu->setChildrenAttribute('class', 'menu');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->addChild(
            $this->securityContext->getToken()->getUser()->getUsername(),
            array('route' => 'fos_user_profile_show')
        );
        $menu->addChild('menu.logged_in.logout', array('route' => 'fos_user_security_logout'));

        return $menu;
    }
}
