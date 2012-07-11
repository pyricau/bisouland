<?php

namespace Bisouland\FrontBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');

        $menu->addChild('Accueil', array('route' => 'homepage'));
        $menu->addChild('Amoureux', array('route' => 'lovers'));
        $menu->addChild('News', array('route' => 'news'));

        return $menu;
    }
}
