<?php

namespace Bisouland\BeingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
