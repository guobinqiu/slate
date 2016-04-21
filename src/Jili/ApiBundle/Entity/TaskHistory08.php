<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskHistory08
 *
 * @ORM\Table(name="task_history08")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\TaskHistoryRepository")
 */
class TaskHistory08 //extends TaskHistoryBase
{
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
     */
    private $orderId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $taskType;

    /**
     * @var integer
     */
    private $categoryType;

    /**
     * @var string
     */
    private $taskName;

    /**
     * @var float
     */
    private $rewardPercent;

    /**
     * @var integer
     */
    private $point;

    /**
     * @var \DateTime
     */
    private $ocdCreatedDate;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var integer
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
     * @return TaskHistory08
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
