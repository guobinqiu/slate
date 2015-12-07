<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameEggsBrokenLog
 *
 * @ORM\Table(name="game_eggs_broken_log", indexes={@ORM\Index(name="idx_user_at", columns={"user_id", "created_at", "points_acquired"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GameEggsBrokenLogRepository")
 */
class GameEggsBrokenLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="egg_type", type="integer", nullable=false)
     */
    private $eggType;

    /**
     * @var integer
     *
     * @ORM\Column(name="points_acquired", type="integer", nullable=false)
     */
    private $pointsAcquired;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
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


    public function __construct() 
    {
        $this->setCreatedAt(new \DateTime() );
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return GameEggsBrokenLog
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
     * Set eggType
     *
     * @param integer $eggType
     * @return GameEggsBrokenLog
     */
    public function setEggType($eggType)
    {
        $this->eggType = $eggType;

        return $this;
    }

    /**
     * Get eggType
     *
     * @return integer 
     */
    public function getEggType()
    {
        return $this->eggType;
    }

    /**
     * Set pointsAcquired
     *
     * @param integer $pointsAcquired
     * @return GameEggsBrokenLog
     */
    public function setPointsAcquired($pointsAcquired)
    {
        $this->pointsAcquired = $pointsAcquired;

        return $this;
    }

    /**
     * Get pointsAcquired
     *
     * @return integer 
     */
    public function getPointsAcquired()
    {
        return $this->pointsAcquired;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return GameEggsBrokenLog
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