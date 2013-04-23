<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blackusers
 */
class Blackusers
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var \DateTime
     */
    private $blacked_date;

    /**
     * @var int
     */
    private $status;


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
     * Set user_id
     *
     * @param \int $userId
     * @return Blackusers
     */
    public function setUserId(\int $userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return \int 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set blacked_date
     *
     * @param \DateTime $blackedDate
     * @return Blackusers
     */
    public function setBlackedDate($blackedDate)
    {
        $this->blacked_date = $blackedDate;
    
        return $this;
    }

    /**
     * Get blacked_date
     *
     * @return \DateTime 
     */
    public function getBlackedDate()
    {
        return $this->blacked_date;
    }

    /**
     * Set status
     *
     * @param \int $status
     * @return Blackusers
     */
    public function setStatus(\int $status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return \int 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
