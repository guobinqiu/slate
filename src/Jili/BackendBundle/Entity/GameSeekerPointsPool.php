<?php

namespace Jili\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameSeekerPointsPool
 * status on ( is_valid, is_published) composition
 *
 *     0 0 init 
 *     1 0 topublish
 *     1 1 published
 *     0 1 restored  
 * @ORM\Table(name="game_seeker_points_pool", indexes={@ORM\Index(name="pts_freq", columns={"points", "send_frequency"})})
 * @ORM\Entity(repositoryClass="Jili\BackendBundle\Repository\GameSeekerPointsPoolRepository")
 */
class GameSeekerPointsPool
{
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer", options={"comment": "每次发放的积分"})
     */
    private $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_frequency", type="integer", options={"comment": "发放的频率"})
     */
    private $sendFrequency;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_published", type="boolean", options={"comment": "是否已经发布,1: wrote into cache file,   "})
     */
    private $isPublished;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", options={"comment": "发布日期, auto publish"})
     */
    private $publishedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean", options={"comment": "是否生效, default 0"})
     */
    private $isValid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", options={"comment": "更新日期, if has latest updated_at than cache ,do auto publish"})
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", options={"comment": "创建日期"})
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
     * Set points
     *
     * @param integer $points
     * @return GameSeekerPointsPool
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set sendFrequency
     *
     * @param integer $sendFrequency
     * @return GameSeekerPointsPool
     */
    public function setSendFrequency($sendFrequency)
    {
        $this->sendFrequency = $sendFrequency;

        return $this;
    }

    /**
     * Get sendFrequency
     *
     * @return integer 
     */
    public function getSendFrequency()
    {
        return $this->sendFrequency;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return GameSeekerPointsPool
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return GameSeekerPointsPool
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set isValid
     *
     * @param boolean $isValid
     * @return GameSeekerPointsPool
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get isValid
     *
     * @return boolean 
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return GameSeekerPointsPool
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
     * @return GameSeekerPointsPool
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

    public function __construct() 
    {
        $this->setIsPublished(0);
        $this->setIsValid(0);
        $era= new \DateTime();
        $era->setTimestamp(0);
        $this->setPublishedAt($era);
    }
}

