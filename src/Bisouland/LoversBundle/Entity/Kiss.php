<?php

namespace Bisouland\LoversBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Bisouland\RolePlayingGameSystemBundle\Entity\Attack;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bisouland\LoversBundle\Repository\KissRepository")
 */
class Kiss extends Attack
{
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
}
