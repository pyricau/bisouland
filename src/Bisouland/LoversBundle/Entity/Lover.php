<?php

namespace Bisouland\LoversBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

use Bisouland\RolePlayingGameSystemBundle\Entity\Being;
use Bisouland\BonusBundle\Entity\Bonus;
use Bisouland\RolePlayingGameSystemBundle\Entity\Attack;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bisouland\LoversBundle\Repository\LoverRepository")
 */
class Lover extends Being
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Bisouland\LoversBundle\Entity\Kiss", mappedBy="attacker")
     */
    protected $attacksDone;

    /**
     * @ORM\OneToMany(targetEntity="Bisouland\LoversBundle\Entity\Kiss", mappedBy="defender")
     */
    protected $defensesDone;

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
        $this->bonuses = new ArrayCollection();
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    public function addBonus(Bonus $bonus)
    {
        $this->bonuses[] = $bonus;
    }

    public function getBonuses()
    {
        return $this->bonuses;
    }

    public function getLifePointsLeft()
    {
        $timeSinceLastUpdateInSeconds = time() - $this->updated->getTimestamp();

        return $this->life_points - $timeSinceLastUpdateInSeconds;
    }

    public function getCreated()
    {
        return $this->created;
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
