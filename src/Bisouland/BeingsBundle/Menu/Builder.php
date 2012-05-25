<?php

namespace Bisouland\BeingsBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

use Bisouland\BeingsBundle\Controller\SelectionController;

class Builder extends ContainerAware
{
    public function selectedMenu(FactoryInterface $factory, array $options)
    {
        $request = $this->container->get('request');
        $selectedBeingName = $request->getSession()->get(SelectionController::$sessionKey);

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->addChild('selectedBeing', array(
                    'route' => 'beings_view',
                    'routeParameters' => array('name' => $selectedBeingName),
                    'label' => $selectedBeingName,
        ));

        return $menu;
    }
}
