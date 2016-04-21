<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointHistory02
 *
 * @ORM\Table(name="point_history02")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\PointHistoryRepository")
 */
class PointHistory02 //extends PointHistoryBase
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $pointChangeNum;

    /**
     * @var integer
     */
    private $reason;

    /**
     * @var \DateTime
     */
    private $createTime;


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
     * @param integer $pointChangeNum
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

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return PointHistory02
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
