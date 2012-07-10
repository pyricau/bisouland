<?php

namespace Bisouland\LoversBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

use Bisouland\LoversBundle\Controller\SelectionController;

class Builder extends ContainerAware
{
    private $request;
    private $menu;

    public function selectedMenu(FactoryInterface $factory, array $options)
    {
        $this->request = $request = $this->container->get('request');
        $selectedLover = $this->getSelectedLover();
        $loverName = $selectedLover->getName();

        $this->menu = $factory->createItem('root');
        $this->menu->setChildrenAttribute('class', 'nav');

        $this->addSelectedNameChild($loverName);
        $this->addSelectedLovePointsChild($loverName, $selectedLover->getLovePoints());

        return $this->menu;
    }

    private function getSelectedLover()
    {
        $selectedLoverName = $this->request->getSession()->get(SelectionController::$sessionKey);

        $doctrine = $this->container->get('doctrine');
        $lover = $doctrine->getRepository('BisoulandGameSystemBundle:Lover')
                ->findOneByName($selectedLoverName);

        return $lover;
    }

    private function addSelectedNameChild($name)
    {
        $this->menu->addChild('selectedName', array(
            'route' => 'lovers_view',
            'routeParameters' => array('name' => $name),
            'label' => $name,
            'extras' => array('safe_label' => true)
        ));
    }

    private function addSelectedLovePointsChild($name, $lovePoints)
    {
        $this->menu->addChild('selectedLovePoints', array(
            'route' => 'lovers_view',
            'routeParameters' => array('name' => $name),
            'label' => sprintf(
                    '<span class="number">'
                        .'%s'
                    .'</span>'
                    .' <span class="badge">'
                        .'<abbr class="initialism" title="Points d\'Amour">'
                            .'PA'
                        .'</abbr>'
                    .'</span>',
                    $lovePoints
            ),
            'extras' => array('safe_label' => true)
        ));
    }
}
