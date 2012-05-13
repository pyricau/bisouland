<?php

namespace Bisouland\BeingsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bisouland\BeingsBundle\Entity\Being;

class LoadBeingData implements FixtureInterface
{
    static public function getFixtures()
    {
        $numberOfSecondsInOneDay = 24 * 60 * 60;
    
        $beings = array(
            'smith' => array('name' => 'Smith', 'love_points' => 1 * $numberOfSecondsInOneDay),
            'john' => array('name' => 'John', 'love_points' => 2 * $numberOfSecondsInOneDay),
            'adam' => array('name' => 'Adam', 'love_points' => 3 * $numberOfSecondsInOneDay),
            'douglas' => array('name' => 'Douglas', 'love_points' => 4 * $numberOfSecondsInOneDay),
            'terry' => array('name' => 'Terry', 'love_points' => 5 * $numberOfSecondsInOneDay),
        );
        
        return $beings;
    }

    public function load(ObjectManager $manager)
    {
        $beings = self::getFixtures();
        foreach ($beings as $being) {
            $newBeing = new Being();
            $newBeing->setName($being['name']);
            $newBeing->setLovePoints($being['love_points']);
            
            $manager->persist($newBeing);
        }
        $manager->flush();
    }
}
