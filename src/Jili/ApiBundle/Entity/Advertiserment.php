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
	public function __construct() {
		$this->createdTime = new \DateTime();
		$this->updateTime = new \DateTime();
		$this->startTime = new \DateTime();
		$this->endTime = new \DateTime();
	}
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
     * @var text
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="imageurl", type="string", length=250)
     */
    private $imageurl;
    
    /**
     * @var string
     *
     * @ORM\Column(name="icon_image", type="string", length=250)
     */
    private $iconImage;

    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_type", type="integer")
     */
    private $incentiveType;

    /**
     * @var text
     *
     * @ORM\Column(name="info", type="text")
     */
    private $info;

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
     * @param text $content
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
     * @return text 
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
     * Set iconImage
     *
     * @param string $iconImage
     * @return Advertiserment
     */
    public function setIconImage($iconImage)
    {
    	$this->iconImage = $iconImage;
    
    	return $this;
    }
    
    /**
     * Get iconImage
     *
     * @return string
     */
    public function getIconImage()
    {
    	return $this->iconImage;
    }
    
    
    /**
     * Set incentiveType
     *
     * @param integer $incentiveType
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
     * @return integer 
     */
    public function getIncentiveType()
    {
        return $this->incentiveType;
    }

    /**
     * Set info
     *
     * @param text $info
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
     * @return text 
     */
    public function getInfo()
    {
        return $this->info;
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
