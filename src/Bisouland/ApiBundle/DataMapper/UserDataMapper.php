<?php

namespace Bisouland\ApiBundle\DataMapper;

use Bisouland\ApiBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserDataMapper
{
    /** @var ObjectManager */
    private $objectManager;

    /** @param ObjectManager $objectManager */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /** @param User $user */
    public function insert(User $user)
    {
        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }
}
