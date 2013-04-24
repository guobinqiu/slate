<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointHistory02
 *
 * @ORM\Table(name="point_history02")
 * @ORM\Entity
 */
class PointHistory02
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
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="point_change_num", type="string", length=45, nullable=true)
     */
    private $pointChangeNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="reason", type="integer", nullable=true)
     */
    private $reason;



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
     * @return PointHistory02
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
     * Set pointChangeNum
     *
     * @param string $pointChangeNum
     * @return PointHistory02
     */
    public function setPointChangeNum($pointChangeNum)
    {
        $this->pointChangeNum = $pointChangeNum;
    
        return $this;
    }

    /**
     * Get pointChangeNum
     *
     * @return string 
     */
    public function getPointChangeNum()
    {
        return $this->pointChangeNum;
    }

    /**
     * Set reason
     *
     * @param integer $reason
     * @return PointHistory02
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    
        return $this;
    }

    /**
     * Get reason
     *
     * @return integer 
     */
    public function getReason()
    {
        return $this->reason;
    }
}