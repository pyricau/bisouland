<?php

namespace Bisouland\BonusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bisouland\LoversBundle\Entity\Lover;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Bonus
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Bisouland\LoversBundle\Entity\Lover", inversedBy="bonuses")
     * @ORM\JoinColumn(name="lover_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $lover;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    public function getId()
    {
        return $this->id;
    }

    public function setLover(Lover $lover)
    {
        $this->lover = $lover;
        return $this;
    }

    public function getLover()
    {
        return $this->lover;
    }

    public function getCreated()
    {
        return $this->created;
    }
}