<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskHistory04
 *
 * @ORM\Table(name="task_history04")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\TaskHistoryRepository")
 */
class TaskHistory04
{
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
     * @var integer
     *
     * @ORM\Column(name="point", type="integer")
     */
    private $point;

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
     * @return TaskHistory04
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
     * @return TaskHistory04
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
     * @return TaskHistory04
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
     * @return TaskHistory04
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
     * @return TaskHistory04
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
     * Set point
     *
     * @param integer $point
     * @return TaskHistory04
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
     * Set date
     *
     * @param \DateTime $date
     * @return TaskHistory04
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
     * @return TaskHistory04
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
