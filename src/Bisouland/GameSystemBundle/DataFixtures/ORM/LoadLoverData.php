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
                'heart_bonus' => 1,
                'slap_bonus' => 1,
                'level' => 1,
            ),
        );

        foreach ($lovers as $lover) {
            $newLover = new lover();
            $newLover
                ->setName($lover['name'])
                ->setLovePoints($lover['love_points'])
                ->setSeductionBonus($lover['seduction_bonus'])
                ->setTongueBonus($lover['tongue_bonus'])
                ->setHeartBonus($lover['heart_bonus'])
                ->setSlapBonus($lover['slap_bonus'])
                ->setLevel($lover['level']);

            $manager->persist($newLover);
        }
        $manager->flush();
    }
}
