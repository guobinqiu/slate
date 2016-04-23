<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointHistory08
 *
 * @ORM\Table(name="point_history08", indexes={@ORM\Index(name="fk_point_history_00_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\PointHistoryRepository")
 */
class PointHistory08
{

    public function __construct()
    {
        $this->createTime = new \DateTime();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="point_change_num", type="integer")
     */
    private $pointChangeNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="reason", type="integer")
     */
    private $reason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true)
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
     * @return PointHistoryXX
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
     * @return PointHistoryXX
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
     * @return PointHistoryXX
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
     * @return PointHistoryXX
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
