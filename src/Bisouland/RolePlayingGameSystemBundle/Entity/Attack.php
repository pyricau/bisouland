<?php

namespace Bisouland\RolePlayingGameSystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;

/**
 * @ORM\MappedSuperclass
 */
class Attack
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="attacker_earning", type="integer")
     */
    protected $attacker_earning;

    /**
     * @ORM\Column(name="defender_loss", type="integer")
     */
    protected $defender_loss;

    /**
     * @ORM\Column(name="is_critical", type="boolean")
     */
    protected $is_critical;

    /**
     * @ORM\Column(name="has_hit", type="boolean")
     */
    protected $has_hit;

    protected $attacker;
    protected $defender;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

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
