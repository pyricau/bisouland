<?php

namespace Bisouland\BeingsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bisouland\BeingsBundle\Entity\Being;

class LoadBeingData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $names = array(
            'Smith',
            'John',
            'Adam',
            'Douglas',
            'Terry',
        );
        
        foreach ($names as $name) {
            $being = new Being();
            $being->setName($name);
            
            $manager->persist($being);
        }
        $manager->flush();
    }
}
