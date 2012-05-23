<?php

namespace Bisouland\BeingsBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function selectedMenu(FactoryInterface $factory, array $options)
    {
        $request = $this->container->get('request');
        $selectedBeingName = $request->getSession()->get('nameOfSelectedBeing');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->addChild($selectedBeingName, array(
                    'route' => 'beings_view',
                    'routeParameters' => array('name' => $selectedBeingName),
        ));

        return $menu;
    }
}
