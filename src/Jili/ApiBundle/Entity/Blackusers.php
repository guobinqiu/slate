<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlackUsers
 *
 * @ORM\Table(name="black_users")
 * @ORM\Entity
 */
class BlackUsers
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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="blacked_date", type="datetime", nullable=true)
     */
    private $blackedDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
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
     * Set userId
     *
     * @param integer $userId
     * @return BlackUsers
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set blackedDate
     *
     * @param \DateTime $blackedDate
     * @return BlackUsers
     */
    public function setBlackedDate($blackedDate)
    {
        $this->blackedDate = $blackedDate;
    
        return $this;
    }

    /**
     * Get blackedDate
     *
     * @return \DateTime 
     */
    public function getBlackedDate()
    {
        return $this->blackedDate;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return BlackUsers
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}