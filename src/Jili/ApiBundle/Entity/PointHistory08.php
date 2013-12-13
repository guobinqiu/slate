<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointHistory08
 *
 * @ORM\Table(name="point_history08")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\PointHistoryRepository")
 */
class PointHistory08
{
	public function __construct() {
		$this->createTime = new \DateTime();
	}
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
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
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
     * @return PointHistory08
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
     * @return PointHistory08
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
     * @return PointHistory08
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
     * @return PointHistory08
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