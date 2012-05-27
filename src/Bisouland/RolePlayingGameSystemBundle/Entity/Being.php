<?php

namespace Bisouland\RolePlayingGameSystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

use Bisouland\RolePlayingGameSystemBundle\Entity\Attack;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Being
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", unique=true, type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="life_points", type="integer")
     */
    private $life_points;

    /**
     * @ORM\Column(name="attack", type="integer")
     */
    private $attack;

    /**
     * @ORM\Column(name="defense", type="integer")
     */
    private $defense;

    /**
     * @ORM\Column(name="constitution", type="integer")
     */
    private $constitution;

    /**
     * @ORM\OneToMany(targetEntity="Attack", mappedBy="attacker")
     */
    private $attacksDone;

    /**
     * @ORM\OneToMany(targetEntity="Attack", mappedBy="defender")
     */
    private $defensesDone;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    public function __construct()
    {
        $this->attacksDone = new ArrayCollection();
        $this->defensesDone = new ArrayCollection();
        $this->bonuses = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLifePoints($life_points)
    {
        $this->life_points = $life_points;
        return $this;
    }

    public function getLifePoints()
    {
        return $this->life_points;
    }

    public function setAttack($attack)
    {
        $this->attack = $attack;
        return $this;
    }

    public function getBonusAttack()
    {
        return $this->getBonusFromGivenAttribute($this->attack);
    }

    public function setDefense($defense)
    {
        $this->defense = $defense;
        return $this;
    }

    public function getBonusDefense()
    {
        return $this->getBonusFromGivenAttribute($this->defense);
    }

    public function setConstitution($constitution)
    {
        $this->constitution = $constitution;
        return $this;
    }
    
    public function getBonusConstitution()
    {
        return $this->getBonusFromGivenAttribute($this->constitution);
    }

    private function getBonusFromGivenAttribute($attributePoints)
    {
        return intval(($attributePoints - 10) / 2);
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getAge()
    {
        return time() - $this->created->getTimestamp();
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function addAttackDone(Attack $attackDone)
    {
        $this->attacksDone[] = $attackDone;
    }

    public function getAttacksDone()
    {
        return $this->attacksDone;
    }

    public function addDefenseDone(Attack $defenseDone)
    {
        $this->defensesDone[] = $defenseDone;
    }

    public function getDefensesDone()
    {
        return $this->defensesDone;
    }
}
