<?php

namespace Bisouland\RolePlayingGameSystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Attack
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="attacker_earning", type="integer")
     */
    private $attacker_earning;

    /**
     * @ORM\Column(name="defender_loss", type="integer")
     */
    private $defender_loss;

    /**
     * @ORM\Column(name="is_critical", type="boolean")
     */
    private $is_critical;

    /**
     * @ORM\Column(name="has_hit", type="boolean")
     */
    private $has_hit;

    /**
     * @ORM\ManyToOne(targetEntity="Being", inversedBy="attacksDone")
     * @ORM\JoinColumn(name="attacker_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attacker;

    /**
     * @ORM\ManyToOne(targetEntity="Being", inversedBy="defensesDone")
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

    public function getAttackerEarning()
    {
        return $this->attacker_earning;
    }

    public function setAttackerEarning($attacker_earning)
    {
        $this->attacker_earning = $attacker_earning;
        return $this;
    }

    public function getDefenderLoss()
    {
        return $this->defender_loss;
    }

    public function setDefenderLoss($defender_loss)
    {
        $this->defender_loss = $defender_loss;
        return $this;
    }

    public function getIsCritical()
    {
        return $this->is_critical;
    }

    public function setIsCritical($is_critical)
    {
        $this->is_critical = $is_critical;
        return $this;
    }

    public function getHasHit()
    {
        return $this->has_hit;
    }

    public function setHasHit($has_hit)
    {
        $this->has_hit = $has_hit;
        return $this;
    }

    public function setAttacker(Being $attacker = null)
    {
        $this->attacker = $attacker;
        return $this;
    }

    public function getAttacker()
    {
        return $this->attacker;
    }

    public function setDefender(Being $defender = null)
    {
        $this->defender = $defender;
        return $this;
    }

    public function getDefender()
    {
        return $this->defender;
    }
}
