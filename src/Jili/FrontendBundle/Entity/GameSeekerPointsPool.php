<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameSeekerPointsPool
 *
 * @ORM\Table(name="game_seeker_points_pool", indexes={@ORM\Index(name="pts_freq", columns={"points", "send_frequency"})})
 * @ORM\Entity
 */
class GameSeekerPointsPool
{
    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_frequency", type="integer", nullable=false)
     */
    private $sendFrequency;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=false)
     */
    private $isPublished;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=false)
     */
    private $isValid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
