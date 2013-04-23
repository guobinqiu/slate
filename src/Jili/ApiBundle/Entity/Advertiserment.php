<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Advertiserment
 */
class Advertiserment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var int
     */
    private $type;
    
    /**
     * @var int
     */
    private $show_flag;

    /**
     * @var varchar
     */
    private $title;

    /**
     * @var \DateTime
     */
    private $created_time;

    /**
     * @var \DateTime
     */
    private $start_time;

    /**
     * @var \DateTime
     */
    private $end_time;

    /**
     * @var \DateTime
     */
    private $update_time;

    /**
     * @var varchar
     */
    private $content;

    /**
     * @var varchar
     */
    private $imageurl;

    /**
     * @var string
     */
    private $incentive;

    /**
     * @var varchar
     */
    private $_type;

    /**
     * @var varchar
     */
    private $info;

    /**
     * @var varchar
     */
    private $income;

    /**
     * @var int
     */
    private $delete_flag;


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
     * @param \int $type
     * @return Advertiserment
     */
    public function setType(\int $type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \int 
     */
    public function getType()
    {
        return $this->type;
    }
    
    
    /**
     * Set show_flag
     *
     * @param \int $type
     * @return Advertiserment
     */
    public function setShow_flag(\int $show_flag)
    {
    	$this->show_flag = $show_flag;
    
    	return $this;
    }
    
    /**
     * Get show_flag
     *
     * @return \int
     */
    public function getShow_flag()
    {
    	return $this->show_flag;
    }

    /**
     * Set title
     *
     * @param \varchar $title
     * @return Advertiserment
     */
    public function setTitle(\varchar $title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return \varchar 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set created_time
     *
     * @param \DateTime $createdTime
     * @return Advertiserment
     */
    public function setCreatedTime($createdTime)
    {
        $this->created_time = $createdTime;
    
        return $this;
    }

    /**
     * Get created_time
     *
     * @return \DateTime 
     */
    public function getCreatedTime()
    {
        return $this->created_time;
    }

    /**
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return Advertiserment
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;
    
        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     * @return Advertiserment
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;
    
        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set update_time
     *
     * @param \DateTime $updateTime
     * @return Advertiserment
     */
    public function setUpdateTime($updateTime)
    {
        $this->update_time = $updateTime;
    
        return $this;
    }

    /**
     * Get update_time
     *
     * @return \DateTime 
     */
    public function getUpdateTime()
    {
        return $this->update_time;
    }

    /**
     * Set content
     *
     * @param \varchar $content
     * @return Advertiserment
     */
    public function setContent(\varchar $content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return \varchar 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set imageurl
     *
     * @param \varchar $imageurl
     * @return Advertiserment
     */
    public function setImageurl(\varchar $imageurl)
    {
        $this->imageurl = $imageurl;
    
        return $this;
    }

    /**
     * Get imageurl
     *
     * @return \varchar 
     */
    public function getImageurl()
    {
        return $this->imageurl;
    }

    /**
     * Set incentive
     *
     * @param string $incentive
     * @return Advertiserment
     */
    public function setIncentive($incentive)
    {
        $this->incentive = $incentive;
    
        return $this;
    }

    /**
     * Get incentive
     *
     * @return string 
     */
    public function getIncentive()
    {
        return $this->incentive;
    }

    /**
     * Set info
     *
     * @param \varchar $info
     * @return Advertiserment
     */
    public function setInfo(\varchar $info)
    {
        $this->info = $info;
    
        return $this;
    }

    /**
     * Get info
     *
     * @return \varchar 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set income
     *
     * @param \varchar $income
     * @return Advertiserment
     */
    public function setIncome(\varchar $income)
    {
        $this->income = $income;
    
        return $this;
    }

    /**
     * Get income
     *
     * @return \varchar 
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set delete_flag
     *
     * @param \int $deleteFlag
     * @return Advertiserment
     */
    public function setDeleteFlag(\int $deleteFlag)
    {
        $this->delete_flag = $deleteFlag;
    
        return $this;
    }

    /**
     * Get delete_flag
     *
     * @return \int 
     */
    public function getDeleteFlag()
    {
        return $this->delete_flag;
    }
}
