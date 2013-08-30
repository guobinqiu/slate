<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HobbyList
 *
 * @ORM\Table(name="hobby_list")
 * @ORM\Entity
 */
class HobbyList
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="hobby_name", type="string", length=250, nullable=true)
     */
    private $hobbyName;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hobbyName
     *
     * @param string $hobbyName
     * @return HobbyList
     */
    public function setHobbyName($hobbyName)
    {
        $this->hobbyName = $hobbyName;
    
        return $this;
    }

    /**
     * Get hobbyName
     *
     * @return string 
     */
    public function getHobbyName()
    {
        return $this->hobbyName;
    }
    
}
