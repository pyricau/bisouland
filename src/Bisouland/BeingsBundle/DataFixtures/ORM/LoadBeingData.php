<?php

namespace Bisouland\BeingsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bisouland\BeingsBundle\Entity\Being;

class LoadBeingData implements FixtureInterface
{
    static public function getFixtures()
    {
        $beings = array(
            'smith' => array('name' => 'Smith'),
            'john' => array('name' => 'John'),
            'adam' => array('name' => 'Adam'),
            'douglas' => array('name' => 'Douglas'),
            'terry' => array('name' => 'Terry'),
        );
        
        return $beings;
    }

    public function load(ObjectManager $manager)
    {
        $beings = self::getFixtures();
        foreach ($beings as $being) {
            $newBeing = new Being();
            $newBeing->setName($being['name']);
            
            $manager->persist($newBeing);
        }
        $manager->flush();
    }
}
