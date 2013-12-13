<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckinClickList
 *
 * @ORM\Table(name="checkin_click_list")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\CheckinClickListRepository")
 */
class CheckinClickList
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="click_date", type="string" ,length=20)
     */
    private $clickDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="open_shop_times", type="integer")
     */
    private $openShopTimes;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer",nullable=true)
     */
    private $status;

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
     * @return CheckinClickList
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
     * Set clickDate
     *
     * @param string $clickDate
     * @return CheckinClickList
     */
    public function setClickDate($clickDate)
    {
        $this->clickDate = $clickDate;
    
        return $this;
    }

    /**
     * Get clickDate
     *
     * @return string 
     */
    public function getClickDate()
    {
        return $this->clickDate;
    }


    /**
     * Set openShopTimes
     *
     * @param integer $openShopTimes
     * @return CheckinClickList
     */
    public function setOpenShopTimes($openShopTimes)
    {
        $this->openShopTimes = $openShopTimes;
    
        return $this;
    }

    /**
     * Get openShopTimes
     *
     * @return integer 
     */
    public function getOpenShopTimes()
    {
        return $this->openShopTimes;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CheckinClickList
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

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return CheckinClickList
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
