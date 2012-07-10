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
     * @ORM\Column(name="kisser_earning", type="integer")
     */
    private $kisser_earning;

    /**
     * @ORM\Column(name="kissed_loss", type="integer")
     */
    private $kissed_loss;

    /**
     * @ORM\Column(name="is_critical", type="boolean")
     */
    private $is_critical;

    /**
     * @ORM\Column(name="has_kissed", type="boolean")
     */
    private $has_kissed;

    /**
     * @ORM\ManyToOne(targetEntity="Being", inversedBy="kisses")
     * @ORM\JoinColumn(name="kisser_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $kisser;
    
    /**
     * @ORM\ManyToOne(targetEntity="Being", inversedBy="kissedBy")
     * @ORM\JoinColumn(name="kissed_id", referencedColumnName="id", onDelete="CASCADE")
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
    
    public function getKisserEarning()
    {
        return $this->kisser_earning;
    }
    
    public function setKisserEarning($kisser_earning)
    {
        $this->kisser_earning = $kisser_earning;
        return $this;
    }
    
    public function getKissedLoss()
    {
        return $this->kissed_loss;
    }

    public function setKissedLoss($kissed_loss)
    {
        $this->kissed_loss = $kissed_loss;
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

    public function getHasKissed()
    {
        return $this->has_kissed;
    }

    public function setHasKissed($has_kissed)
    {
        $this->has_kissed = $has_kissed;
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