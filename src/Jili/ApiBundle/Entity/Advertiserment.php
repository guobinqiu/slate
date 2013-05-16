<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Advertiserment
 *
 * @ORM\Table(name="advertiserment")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdvertisermentRepository")
 */
class Advertiserment
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
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="show_flag", type="integer")
     */
    private $showFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_time", type="datetime")
     */
    private $createdTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime")
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_time", type="datetime")
     */
    private $updateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=1000)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="imageurl", type="string", length=45)
     */
    private $imageurl;

    /**
     * @var string
     *
     * @ORM\Column(name="incentive_type", type="string", length=250)
     */
    private $incentiveType;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=45)
     */
    private $info;

    /**
     * @var string
     *
     * @ORM\Column(name="income", type="string", length=45)
     */
    private $income;

    /**
     * @var integer
     *
     * @ORM\Column(name="category", type="integer")
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer")
     */
    private $deleteFlag;

    /**
     * @var float
     *
     * @ORM\Column(name="comm", type="float")
     */
    private $comm;

    /**
     * @var float
     *
     * @ORM\Column(name="totalprice", type="float")
     */
    private $totalprice;

    /**
     * @var string
     *
     * @ORM\Column(name="ocd", type="string", length=1000)
     */
    private $ocd;

    /**
     * @var string
     *
     * @ORM\Column(name="goodspricecount", type="string", length=1000)
     */
    private $goodspricecount;

    /**
     * @var integer
     *
     * @ORM\Column(name="paymentmethod", type="integer")
     */
    private $paymentmethod;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="paid", type="integer")
     */
    private $paid;

    /**
     * @var integer
     *
     * @ORM\Column(name="confirm", type="integer")
     */
    private $confirm;


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
     * Set type
     *
     * @param integer $type
     * @return Advertiserment
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set showFlag
     *
     * @param integer $showFlag
     * @return Advertiserment
     */
    public function setShowFlag($showFlag)
    {
        $this->showFlag = $showFlag;
    
        return $this;
    }

    /**
     * Get showFlag
     *
     * @return integer 
     */
    public function getShowFlag()
    {
        return $this->showFlag;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Advertiserment
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdTime
     *
     * @param \DateTime $createdTime
     * @return Advertiserment
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
    
        return $this;
    }

    /**
     * Get createdTime
     *
     * @return \DateTime 
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Advertiserment
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    
        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Advertiserment
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    
        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     * @return Advertiserment
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    
        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime 
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Advertiserment
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set imageurl
     *
     * @param string $imageurl
     * @return Advertiserment
     */
    public function setImageurl($imageurl)
    {
        $this->imageurl = $imageurl;
    
        return $this;
    }

    /**
     * Get imageurl
     *
     * @return string 
     */
    public function getImageurl()
    {
        return $this->imageurl;
    }

    /**
     * Set incentiveType
     *
     * @param string $incentiveType
     * @return Advertiserment
     */
    public function setIncentiveType($incentiveType)
    {
        $this->incentiveType = $incentiveType;
    
        return $this;
    }

    /**
     * Get incentiveType
     *
     * @return string 
     */
    public function getIncentiveType()
    {
        return $this->incentiveType;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return Advertiserment
     */
    public function setInfo($info)
    {
        $this->info = $info;
    
        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set income
     *
     * @param string $income
     * @return Advertiserment
     */
    public function setIncome($income)
    {
        $this->income = $income;
    
        return $this;
    }

    /**
     * Get income
     *
     * @return string 
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set category
     *
     * @param integer $category
     * @return Advertiserment
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return integer 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return Advertiserment
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;
    
        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return integer 
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
    }

    /**
     * Set comm
     *
     * @param float $comm
     * @return Advertiserment
     */
    public function setComm($comm)
    {
        $this->comm = $comm;
    
        return $this;
    }

    /**
     * Get comm
     *
     * @return float 
     */
    public function getComm()
    {
        return $this->comm;
    }

    /**
     * Set totalprice
     *
     * @param float $totalprice
     * @return Advertiserment
     */
    public function setTotalprice($totalprice)
    {
        $this->totalprice = $totalprice;
    
        return $this;
    }

    /**
     * Get totalprice
     *
     * @return float 
     */
    public function getTotalprice()
    {
        return $this->totalprice;
    }

    /**
     * Set ocd
     *
     * @param string $ocd
     * @return Advertiserment
     */
    public function setOcd($ocd)
    {
        $this->ocd = $ocd;
    
        return $this;
    }

    /**
     * Get ocd
     *
     * @return string 
     */
    public function getOcd()
    {
        return $this->ocd;
    }

    /**
     * Set goodspricecount
     *
     * @param string $goodspricecount
     * @return Advertiserment
     */
    public function setGoodspricecount($goodspricecount)
    {
        $this->goodspricecount = $goodspricecount;
    
        return $this;
    }

    /**
     * Get goodspricecount
     *
     * @return string 
     */
    public function getGoodspricecount()
    {
        return $this->goodspricecount;
    }

    /**
     * Set paymentmethod
     *
     * @param integer $paymentmethod
     * @return Advertiserment
     */
    public function setPaymentmethod($paymentmethod)
    {
        $this->paymentmethod = $paymentmethod;
    
        return $this;
    }

    /**
     * Get paymentmethod
     *
     * @return integer 
     */
    public function getPaymentmethod()
    {
        return $this->paymentmethod;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Advertiserment
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
     * Set paid
     *
     * @param integer $paid
     * @return Advertiserment
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    
        return $this;
    }

    /**
     * Get paid
     *
     * @return integer 
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set confirm
     *
     * @param integer $confirm
     * @return Advertiserment
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    
        return $this;
    }

    /**
     * Get confirm
     *
     * @return integer 
     */
    public function getConfirm()
    {
        return $this->confirm;
    }
}
