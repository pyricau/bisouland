<?php

namespace Bisouland\GameSystemBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Bisouland\GameSystemBundle\Entity\Lover;

class LoadLoverData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $lovers = array(
            'TestLoverForSelfKissing' => array(
                'name' => 'TestLoverForSelfKissing',
                'love_points' => 42,
                'seduction_bonus' => 1,
                'tongue_bonus' => 1,
                'dodge_bonus' => 1,
                'slap_bonus' => 1,
            ),
        );

        foreach ($lovers as $lover) {
            $newLover = new lover();
            $newLover->setName($lover['name']);
            $newLover->setLovePoints($lover['love_points']);
            $newLover->setSeductionBonus($lover['seduction_bonus']);
            $newLover->setTongueBonus($lover['tongue_bonus']);
            $newLover->setDodgeBonus($lover['dodge_bonus']);
            $newLover->setSlapBonus($lover['slap_bonus']);
            
            $manager->persist($newLover);
        }
        $manager->flush();
    }
}
