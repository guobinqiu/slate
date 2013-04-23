<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdwAccessRecord
 */
class AdwAccessRecord
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @var varchar
     */
    private $key;


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
     * Set time
     *
     * @param \DateTime $time
     * @return AdwAccessRecord
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set key
     *
     * @param \varchar $key
     * @return AdwAccessRecord
     */
    public function setKey(\varchar $key)
    {
        $this->key = $key;
    
        return $this;
    }

    /**
     * Get key
     *
     * @return \varchar 
     */
    public function getKey()
    {
        return $this->key;
    }
}
