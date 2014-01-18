<?php

namespace Bisouland\UserBundle\DataFixtures\ORM;

use Bisouland\UserBundle\Entity\User;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * User fixtures, generated from a list of usernames.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return array
     */
    public static function getUsernames()
    {
        return array(
            'admin',
            'simple.user',

            'to.login',
            'to.logout',
            'change',
            'existing',
            'to.remove',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getUsernames() as $username) {
            $user = new User();
            $user->setUsername($username);
            $user->setPlainPassword('password');
            $user->setEmail($username.'@example.com');
            $user->setEnabled(true);

            if (false !== strstr($username, 'admin')) {
                $user->setSuperAdmin(true);
            }

            $manager->persist($user);
            $this->addReference($username, $user);
        }
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 50;
    }
}
