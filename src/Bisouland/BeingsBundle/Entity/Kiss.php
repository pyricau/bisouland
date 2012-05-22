<?php

namespace Bisouland\BeingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bisouland\BeingsBundle\Entity\Being;

/**
 * Bisouland\BeingsBundle\Entity\Attack
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bisouland\BeingsBundle\Repository\KissRepository")
 */
class Kiss
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="damages", type="integer")
     */
    private $damages;
    
    /**
     * @ORM\Column(name="reward", type="integer")
     */
    private $reward;

    /**
     * @ORM\ManyToOne(targetEntity="Being", inversedBy="kisses")
     * @ORM\JoinColumn(name="kisser_id", referencedColumnName="id")
     */
    protected $kisser;
    
    /**
     * @ORM\ManyToOne(targetEntity="Being", inversedBy="kissedBy")
     * @ORM\JoinColumn(name="kissed_id", referencedColumnName="id")
     */
    protected $kissed;
    
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
    
    public function getDamages()
    {
        return $this->damages;
    }
    
    public function setDamages($damages)
    {
        $this->damages = $damages;
        return $this;
    }
    
    public function getReward()
    {
        return $this->reward;
    }
    
    public function setReward($reward)
    {
        $this->reward = $reward;
        return $this;
    }

    public function setKisser(Being $kisser = null)
    {
        $this->kisser = $kisser;
        return $this;
    }

    public function getKisser()
    {
        return $this->kisser;
    }

    public function setKissed(Being $kissed = null)
    {
        $this->kissed = $kissed;
        return $this;
    }

    public function getKissed()
    {
        return $this->kissed;
    }
}