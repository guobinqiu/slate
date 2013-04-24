<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Advertiserment
 *
 * @ORM\Table(name="advertiserment")
 * @ORM\Entity
 */
class Advertiserment
{
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
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="show_flag", type="integer", nullable=false)
     */
    private $showFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45, nullable=true)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_time", type="datetime", nullable=true)
     */
    private $createdTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_time", type="datetime", nullable=true)
     */
    private $updateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=10000, nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="imageurl", type="string", length=45, nullable=true)
     */
    private $imageurl;

    /**
     * @var string
     *
     * @ORM\Column(name="incentive _type", type="string", length=45, nullable=true)
     */
    private $incentiveType;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=45, nullable=true)
     */
    private $info;

    /**
     * @var string
     *
     * @ORM\Column(name="income", type="string", length=45, nullable=true)
     */
    private $income;

    /**
     * @var integer
     *
     * @ORM\Column(name="category", type="integer", nullable=false)
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=true)
     */
    private $deleteFlag;



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
}