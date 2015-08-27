<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoteImage
 *
 * @ORM\Table(name="vote_image", uniqueConstraints={@ORM\UniqueConstraint(name="vote_uk", columns={"vote_id"})})
 * @ORM\Entity
 */
class VoteImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="vote_id", type="integer", nullable=false)
     */
    private $voteId;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="sq_path", type="string", length=255, nullable=true)
     */
    private $sqPath;

    /**
     * @var integer
     *
     * @ORM\Column(name="sq_width", type="integer", nullable=true)
     */
    private $sqWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="sq_height", type="integer", nullable=true)
     */
    private $sqHeight;

    /**
     * @var string
     *
     * @ORM\Column(name="s_path", type="string", length=255, nullable=true)
     */
    private $sPath;

    /**
     * @var integer
     *
     * @ORM\Column(name="s_width", type="integer", nullable=true)
     */
    private $sWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="s_height", type="integer", nullable=true)
     */
    private $sHeight;

    /**
     * @var string
     *
     * @ORM\Column(name="m_path", type="string", length=255, nullable=true)
     */
    private $mPath;

    /**
     * @var integer
     *
     * @ORM\Column(name="m_width", type="integer", nullable=true)
     */
    private $mWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="m_height", type="integer", nullable=true)
     */
    private $mHeight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="delete_flag", type="boolean", nullable=true)
     */
    private $deleteFlag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set voteId
     *
     * @param integer $voteId
     * @return VoteImage
     */
    public function setVoteId($voteId)
    {
        $this->voteId = $voteId;
    
        return $this;
    }

    /**
     * Get voteId
     *
     * @return integer 
     */
    public function getVoteId()
    {
        return $this->voteId;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return VoteImage
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return VoteImage
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return VoteImage
     */
    public function setWidth($width)
    {
        $this->width = $width;
    
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return VoteImage
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set sqPath
     *
     * @param string $sqPath
     * @return VoteImage
     */
    public function setSqPath($sqPath)
    {
        $this->sqPath = $sqPath;
    
        return $this;
    }

    /**
     * Get sqPath
     *
     * @return string 
     */
    public function getSqPath()
    {
        return $this->sqPath;
    }

    /**
     * Set sqWidth
     *
     * @param integer $sqWidth
     * @return VoteImage
     */
    public function setSqWidth($sqWidth)
    {
        $this->sqWidth = $sqWidth;
    
        return $this;
    }

    /**
     * Get sqWidth
     *
     * @return integer 
     */
    public function getSqWidth()
    {
        return $this->sqWidth;
    }

    /**
     * Set sqHeight
     *
     * @param integer $sqHeight
     * @return VoteImage
     */
    public function setSqHeight($sqHeight)
    {
        $this->sqHeight = $sqHeight;
    
        return $this;
    }

    /**
     * Get sqHeight
     *
     * @return integer 
     */
    public function getSqHeight()
    {
        return $this->sqHeight;
    }

    /**
     * Set sPath
     *
     * @param string $sPath
     * @return VoteImage
     */
    public function setSPath($sPath)
    {
        $this->sPath = $sPath;
    
        return $this;
    }

    /**
     * Get sPath
     *
     * @return string 
     */
    public function getSPath()
    {
        return $this->sPath;
    }

    /**
     * Set sWidth
     *
     * @param integer $sWidth
     * @return VoteImage
     */
    public function setSWidth($sWidth)
    {
        $this->sWidth = $sWidth;
    
        return $this;
    }

    /**
     * Get sWidth
     *
     * @return integer 
     */
    public function getSWidth()
    {
        return $this->sWidth;
    }

    /**
     * Set sHeight
     *
     * @param integer $sHeight
     * @return VoteImage
     */
    public function setSHeight($sHeight)
    {
        $this->sHeight = $sHeight;
    
        return $this;
    }

    /**
     * Get sHeight
     *
     * @return integer 
     */
    public function getSHeight()
    {
        return $this->sHeight;
    }

    /**
     * Set mPath
     *
     * @param string $mPath
     * @return VoteImage
     */
    public function setMPath($mPath)
    {
        $this->mPath = $mPath;
    
        return $this;
    }

    /**
     * Get mPath
     *
     * @return string 
     */
    public function getMPath()
    {
        return $this->mPath;
    }

    /**
     * Set mWidth
     *
     * @param integer $mWidth
     * @return VoteImage
     */
    public function setMWidth($mWidth)
    {
        $this->mWidth = $mWidth;
    
        return $this;
    }

    /**
     * Get mWidth
     *
     * @return integer 
     */
    public function getMWidth()
    {
        return $this->mWidth;
    }

    /**
     * Set mHeight
     *
     * @param integer $mHeight
     * @return VoteImage
     */
    public function setMHeight($mHeight)
    {
        $this->mHeight = $mHeight;
    
        return $this;
    }

    /**
     * Get mHeight
     *
     * @return integer 
     */
    public function getMHeight()
    {
        return $this->mHeight;
    }

    /**
     * Set deleteFlag
     *
     * @param boolean $deleteFlag
     * @return VoteImage
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;
    
        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return boolean 
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return VoteImage
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return VoteImage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
