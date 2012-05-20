<?php

namespace Bisouland\BeingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bisouland\BeingsBundle\RandomSystem\Character;

/**
 * Bisouland\BeingsBundle\Entity\Being
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bisouland\BeingsBundle\Repository\BeingRepository")
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
     * @ORM\Column(name="love_points", type="integer")
     */
    private $love_points;
    
    /**
     * @ORM\Column(name="seduction", type="integer")
     */
    private $seduction;
    
    /**
     * @ORM\Column(name="slap", type="integer")
     */
    private $slap;
    
    /**
     * @ORM\Column(name="heart", type="integer")
     */
    private $heart;
    
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
        $timeSinceLastUpdate = time() - $this->updated->getTimestamp();

        return $this->love_points - $timeSinceLastUpdate;
    }
    
    public function setSeduction($seduction)
    {
        $this->seduction = $seduction;
        return $this;
    }

    public function getSeduction()
    {
        return $this->seduction;
    }
    
    public function getBonusSeduction()
    {
        return Character::calculateBonusPointsFromAttributePoints($this->seduction);
    }
    
    public function setSlap($slap)
    {
        $this->slap = $slap;
        return $this;
    }

    public function getSlap()
    {
        return $this->slap;
    }
    
    public function getBonusSlap()
    {
        return Character::calculateBonusPointsFromAttributePoints($this->slap);
    }
    
    public function setHeart($heart)
    {
        $this->heart = $heart;
        return $this;
    }

    public function getHeart()
    {
        return $this->heart;
    }
    
    public function getBonusHeart()
    {
        return Character::calculateBonusPointsFromAttributePoints($this->heart);
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
}
