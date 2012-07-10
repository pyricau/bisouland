<?php

namespace Bisouland\LoversBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bisouland\RolePlayingGameSystemBundle\Entity\Attack;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bisouland\LoversBundle\Repository\KissRepository")
 */
class Kiss extends Attack
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Bisouland\LoversBundle\Entity\Lover", inversedBy="attacksDone")
     * @ORM\JoinColumn(name="attacker_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attacker;

    /**
     * @ORM\ManyToOne(targetEntity="Bisouland\LoversBundle\Entity\Lover", inversedBy="defensesDone")
     * @ORM\JoinColumn(name="defender_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $defender;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    public function getId()
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
