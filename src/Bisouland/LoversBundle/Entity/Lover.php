<?php

namespace Bisouland\LoversBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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

    public function __construct()
    {
        $this->bonuses = new ArrayCollection();
        parent::__construct();
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

    public function getAgeInSeconds()
    {
        return time() - $this->created->getTimestamp();
    }
}
