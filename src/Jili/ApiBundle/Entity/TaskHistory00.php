<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskHistory00
 *
 * @ORM\Table(name="task_history00")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\TaskHistoryRepository")
 */
class TaskHistory00
{
    // 1:cpa, 4:checkin , 2 or 3: 小鸡找米, 5: offer-wow
    const  TASK_TYPE_CPA = 1 ; // CPA 
    const  TASK_TYPE_PAG_CPX = 2; //小鸡找米 广告ad_category.id == 3, 
    const  TASK_TYPE_PAG_POINTS = 3; //小鸡找米 积分码ad_category.id == 4
    const  TASK_TYPE_CHECKIN = 4 ;//签到 
    const  TASK_TYPE_OFFERWOW = 5;  // offerwow
    const  TASK_TYPE_GAME_SEEKER = 6; // 寻宝箱
    const  TASK_TYPE_GAME_EGGS_BREAKER = 7;  // 
    const  TASK_TYPE_DUOMAI  = 8;  // 多麦

// const STATUS_SUCCEED = 1; // 完成状态，积分已发.
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="task_type", type="integer")
     */
    private $taskType;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_type", type="integer")
     */
    private $categoryType;

    /**
     * @var string
     *
     * @ORM\Column(name="task_name", type="string", length=50)
     */
    private $taskName;

    /**
     * @var float
     *
     * @ORM\Column(name="reward_percent", type="float",nullable=true)
     */
    private $rewardPercent;

     /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer")
     */
    private $point;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ocd_created_date", type="datetime", nullable=true)
     */
    private $ocdCreatedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
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
     * Set orderId
     *
     * @param integer $orderId
     * @return TaskHistory00
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TaskHistory00
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
     * Set taskType
     *
     * @param integer $taskType
     * @return TaskHistory00
     */
    public function setTaskType($taskType)
    {
        $this->taskType = $taskType;

        return $this;
    }

    /**
     * Get taskType
     *
     * @return integer
     */
    public function getTaskType()
    {
        return $this->taskType;
    }


     /**
     * Set categoryType
     *
     * @param integer $categoryType
     * @return TaskHistory00
     */
    public function setCategoryType($categoryType)
    {
        $this->categoryType = $categoryType;

        return $this;
    }

    /**
     * Get categoryType
     *
     * @return integer
     */
    public function getCategoryType()
    {
        return $this->categoryType;
    }

    /**
     * Set taskName
     *
     * @param string $taskName
     * @return TaskHistory00
     */
    public function setTaskName($taskName)
    {
        $this->taskName = $taskName;

        return $this;
    }

    /**
     * Get taskName
     *
     * @return string
     */
    public function getTaskName()
    {
        return $this->taskName;
    }

     /**
     * Set rewardPercent
     *
     * @param float $rewardPercent
     * @return TaskHistory00
     */
    public function setRewardPercent($rewardPercent)
    {
        $this->rewardPercent = $rewardPercent;

        return $this;
    }

    /**
     * Get rewardPercent
     *
     * @return float
     */
    public function getRewardPercent()
    {
        return $this->rewardPercent;
    }


    /**
     * Set point
     *
     * @param integer $point
     * @return TaskHistory00
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set ocdCreatedDate
     *
     * @param \DateTime $ocdCreatedDate
     * @return TaskHistory00
     */
    public function setOcdCreatedDate($ocdCreatedDate)
    {
        $this->ocdCreatedDate = $ocdCreatedDate;

        return $this;
    }

    /**
     * Get ocdCreatedDate
     *
     * @return \DateTime
     */
    public function getOcdCreatedDate()
    {
        return $this->ocdCreatedDate;
    }


     /**
     * Set date
     *
     * @param \DateTime $date
     * @return TaskHistory00
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return TaskHistory00
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
