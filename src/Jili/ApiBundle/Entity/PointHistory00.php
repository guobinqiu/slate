<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointHistory00
 *
 * @ORM\Table(name="point_history00")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\PointHistoryRepository")
 */
class PointHistory00 extends PointHistoryBase
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var integer
     */
    protected $pointChangeNum;

    /**
     * @var integer
     */
    protected $reason;

    /**
     * @var \DateTime
     */
    protected $createTime;


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
     * @return PointHistory00
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
     * @param integer $pointChangeNum
     * @return PointHistory00
     */
    public function setPointChangeNum($pointChangeNum)
    {
        $this->pointChangeNum = $pointChangeNum;

        return $this;
    }

    /**
     * Get pointChangeNum
     *
     * @return integer 
     */
    public function getPointChangeNum()
    {
        return $this->pointChangeNum;
    }

    /**
     * Set reason
     *
     * @param integer $reason
     * @return PointHistory00
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

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return PointHistory00
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }
}
