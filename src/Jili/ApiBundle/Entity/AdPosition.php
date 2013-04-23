<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdPosition
 */
class AdPosition
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var int
     */
    private $ad_id;

    /**
     * @var varchar
     */
    private $type;

    /**
     * @var int
     */
    private $position;


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
     * Set ad_id
     *
     * @param \int $adId
     * @return AdPosition
     */
    public function setAdId(\int $adId)
    {
        $this->ad_id = $adId;
    
        return $this;
    }

    /**
     * Get ad_id
     *
     * @return \int 
     */
    public function getAdId()
    {
        return $this->ad_id;
    }

    /**
     * Set type
     *
     * @param \varchar $type
     * @return AdPosition
     */
    public function setType(\varchar $type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \varchar 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set position
     *
     * @param \int $position
     * @return AdPosition
     */
    public function setPosition(\int $position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return \int 
     */
    public function getPosition()
    {
        return $this->position;
    }
}
