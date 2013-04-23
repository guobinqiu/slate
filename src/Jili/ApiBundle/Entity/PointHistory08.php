<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointHistory08
 */
class PointHistory08
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
     * @var varchar
     */
    private $point_change_num;

    /**
     * @var int
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
     * Set user_id
     *
     * @param \int $userId
     * @return PointHistory08
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
     * Set point_change_num
     *
     * @param \varchar $pointChangeNum
     * @return PointHistory08
     */
    public function setPointChangeNum(\varchar $pointChangeNum)
    {
        $this->point_change_num = $pointChangeNum;
    
        return $this;
    }

    /**
     * Get point_change_num
     *
     * @return \varchar 
     */
    public function getPointChangeNum()
    {
        return $this->point_change_num;
    }

    /**
     * Set reason
     *
     * @param \int $reason
     * @return PointHistory08
     */
    public function setReason(\int $reason)
    {
        $this->reason = $reason;
    
        return $this;
    }

    /**
     * Get reason
     *
     * @return \int 
     */
    public function getReason()
    {
        return $this->reason;
    }
}
