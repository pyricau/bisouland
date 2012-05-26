<?php

namespace Bisouland\BeingsBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

use Bisouland\BeingsBundle\Controller\SelectionController;

class Builder extends ContainerAware
{
    private $request;
    private $menu;

    public function selectedMenu(FactoryInterface $factory, array $options)
    {
        $this->request = $request = $this->container->get('request');
        $selectedBeing = $this->getSelectedBeing();
        $beingName = $selectedBeing->getName();

        $this->menu = $factory->createItem('root');
        $this->menu->setChildrenAttribute('class', 'nav');
        $this->menu->setCurrentUri($this->request->getRequestUri());
        
        $this->addSelectedNameChild($beingName);
        $this->addSelectedLovePointsChild($beingName, $selectedBeing->getLovePoints());

        return $this->menu;
    }
    
    private function getSelectedBeing()
    {
        $selectedBeingName = $this->request->getSession()->get(SelectionController::$sessionKey);
        
        $doctrine = $this->container->get('doctrine');
        $being = $doctrine->getRepository('BisoulandBeingsBundle:Being')
                ->findOneByName($selectedBeingName);
        
        return $being;
    }

    private function addSelectedNameChild($name)
    {
        $this->menu->addChild('selectedName', array(
            'route' => 'beings_view',
            'routeParameters' => array('name' => $name),
            'label' => $name,
            'extras' => array('safe_label' => true)
        ));
    }

    private function addSelectedLovePointsChild($name, $lovePoints)
    {
        $this->menu->addChild('selectedLovePoints', array(
            'route' => 'beings_view',
            'routeParameters' => array('name' => $name),
            'label' => sprintf(
                    '%s'
                    .' <span class="badge">'
                        .'<abbr class"initialism" title"Points d\'Amour">'
                            .'PA'
                        .'</abbr>'
                    .'</span>',
                    $lovePoints
            ),
            'extras' => array('safe_label' => true)
        ));
    }
}
