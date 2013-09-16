<?php

namespace Bisouland\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use FOS\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name = "user")
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type = "integer")
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    protected $id;
}
