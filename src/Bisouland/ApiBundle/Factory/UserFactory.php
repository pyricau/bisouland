<?php

namespace Bisouland\ApiBundle\Factory;

use Bisouland\ApiBundle\Entity\User;
use Bisouland\ApiBundle\Criteria\CreateUserCriteria;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFactory
{
    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    /** @param EncoderFactoryInterface $encoderFactory */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param CreateUserCriteria $createUserCriteria
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function make(CreateUserCriteria $createUserCriteria)
    {
        $encoder = $this->encoderFactory->getEncoder('Bisouland\\ApiBundle\\Entity\\User');

        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $password = $encoder->encodePassword($createUserCriteria->plainPassword, $salt);

        return new User($createUserCriteria->username, $password, $salt);
    }
}
