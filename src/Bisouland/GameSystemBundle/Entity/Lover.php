<?php

namespace Bisouland\GameSystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

use Bisouland\GameSystemBundle\Entity\Kiss;
use Bisouland\BonusBundle\Entity\Bonus;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bisouland\LoversBundle\Repository\LoverRepository")
 */
class Lover
{
    static public $nextLevelCostMultiplier = 3600;

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
     * @ORM\Column(name="love_points", type="integer")
     */
    private $love_points;

    /**
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @ORM\Column(name="seduction_bonus", type="integer")
     */
    private $seduction_bonus;

    /**
     * @ORM\Column(name="dodge_bonus", type="integer")
     */
    private $dodge_bonus;

    /**
     * @ORM\Column(name="tongue_bonus", type="integer")
     */
    private $tongue_bonus;

    /**
     * @ORM\Column(name="slap_bonus", type="integer")
     */
    private $slap_bonus;

    /**
     * @ORM\OneToMany(targetEntity="Bisouland\GameSystemBundle\Entity\Kiss", mappedBy="kisser")
     */
    private $kissesDone;

    /**
     * @ORM\OneToMany(targetEntity="Bisouland\GameSystemBundle\Entity\Kiss", mappedBy="kissed")
     */
    private $kissesReceived;

    /**
     * @ORM\OneToMany(targetEntity="Bisouland\BonusBundle\Entity\Bonus", mappedBy="lover")
     */
    private $bonuses;

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
        $this->kissesDone = new ArrayCollection();
        $this->kissesReceived = new ArrayCollection();
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

    public function setLovePoints($love_points)
    {
        $this->love_points = $love_points;
        return $this;
    }

    public function getLovePoints()
    {
        $secondsSinceLastUpdate = time() - $this->updated->getTimestamp();

        return $this->love_points - $secondsSinceLastUpdate;
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getNextLevelCost()
    {
        $cost = 0;
        for ($i = 1; $i < $this->level * 4; $i++) {
             $cost += $i;
        }
        
        return $cost * self::$nextLevelCostMultiplier;
    }

    public function setSeductionBonus($seduction_bonus)
    {
        $this->seduction_bonus = $seduction_bonus;
        return $this;
    }

    public function getSeductionBonus()
    {
        return $this->seduction_bonus;
    }

    public function setDodgeBonus($dodge_bonus)
    {
        $this->dodge_bonus = $dodge_bonus;
        return $this;
    }

    public function getDodgeBonus()
    {
        return $this->dodge_bonus;
    }

    public function setTongueBonus($tongue_bonus)
    {
        $this->tongue_bonus = $tongue_bonus;
        return $this;
    }

    public function getTongueBonus()
    {
        return $this->tongue_bonus;
    }

    public function setSlapBonus($slap_bonus)
    {
        $this->slap_bonus = $slap_bonus;
        return $this;
    }

    public function getSlapBonus()
    {
        return $this->slap_bonus;
    }

    public function addKissDone(Kiss $kissDone)
    {
        $this->kissesDone[] = $kissDone;
    }

    public function getKissesDone()
    {
        return $this->kissesDone;
    }

    public function addKissReceived(Kiss $kissReceived)
    {
        $this->kissesReceived[] = $kissReceived;
    }

    public function getKissesReceived()
    {
        return $this->kissesReceived;
    }
    public function addBonus(Bonus $bonus)
    {
        $this->bonuses[] = $bonus;
    }

    public function getBonuses()
    {
        return $this->bonuses;
    }

    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
        return $this;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getAgeInSeconds()
    {
        return time() - $this->created->getTimestamp();
    }
}
