<?php

namespace Bisouland\GameSystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bisouland\GameSystemBundle\Entity\Lover;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bisouland\LoversBundle\Repository\KissRepository")
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
     * @ORM\Column(name="is_critical", type="boolean")
     */
    private $is_critical;

    /**
     * @ORM\Column(name="has_succeeded", type="boolean")
     */
    private $has_succeeded;

    /**
     * @ORM\ManyToOne(targetEntity="Bisouland\GameSystemBundle\Entity\Lover", inversedBy="kissesDone")
     * @ORM\JoinColumn(name="kisser_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $kisser;

    /**
     * @ORM\ManyToOne(targetEntity="Bisouland\GameSystemBundle\Entity\Lover", inversedBy="kissesReceived")
     * @ORM\JoinColumn(name="kissed_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $kissed;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    public function getId()
    {
        return $this->id;
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

    public function getIsCritical()
    {
        return $this->is_critical;
    }

    public function setIsCritical($is_critical)
    {
        $this->is_critical = $is_critical;
        return $this;
    }

    public function getHasSucceeded()
    {
        return $this->has_succeeded;
    }

    public function setHasSucceeded($has_succeeded)
    {
        $this->has_succeeded = $has_succeeded;
        return $this;
    }

    public function setKisser(Lover $kisser = null)
    {
        $this->kisser = $kisser;
        return $this;
    }

    public function getKisser()
    {
        return $this->kisser;
    }

    public function setKissed(Lover $kissed = null)
    {
        $this->kissed = $kissed;
        return $this;
    }

    public function getKissed()
    {
        return $this->kissed;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
