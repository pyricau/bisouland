<?php

namespace Bisouland\ApiBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name = "user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type = "integer", nullable = false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type = "array", nullable = false)
     *
     * @var array
     */
    private $roles;

    /**
     * @ORM\Column(type = "string", nullable = false)
     *
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type = "string", nullable = false)
     *
     * @var string
     */
    private $salt;

    /**
     * @ORM\Column(type = "string", unique = true, nullable = false)
     *
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type = "integer", nullable = false)
     *
     * @var integer
     */
    private $lovePoints;

    /**
     * @ORM\Column(type = "datetime", nullable = false)
     *
     * @var DateTime
     */
    private $createdAt;

    /**
     * @param string $username
     * @param string $password
     * @param string $salt
     */
    public function __construct($username, $password, $salt)
    {
        $this->roles = array('ROLE_USER');
        $this->password = $password;
        $this->salt = $salt;
        $this->username = $username;
        $this->createdAt = new DateTime();
        $this->lovePoints = 300;
    }

    /** @return integer */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getRoles()
    {
        return $this->roles;
    }

    /** {@inheritdoc} */
    public function getPassword()
    {
        return $this->password;
    }

    /** {@inheritdoc} */
    public function getSalt()
    {
        return $this->salt;
    }

    /** {@inheritdoc} */
    public function getUsername()
    {
        return $this->username;
    }

    /** {@inheritdoc} */
    public function eraseCredentials()
    {
    }

    /** @return integer */
    public function getLovePoints()
    {
        $now = time();
        $secondsDiff = $now - $this->createdAt->getTimestamp();

        $producedLovePoints = floor((5500 * exp(-6) * $secondsDiff) / 3600);

        return $this->lovePoints + $producedLovePoints;
    }
}
