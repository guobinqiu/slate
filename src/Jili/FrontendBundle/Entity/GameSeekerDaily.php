<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameSeekerDaily
 *
 * @ORM\Table(name="game_seeker_daily", uniqueConstraints={@ORM\UniqueConstraint(name="token", columns={"token"}), @ORM\UniqueConstraint(name="uid_daily", columns={"user_id", "created_day"})})
 * @ORM\Entity
 */
class GameSeekerDaily
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
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_day", type="date", nullable=true)
     */
    private $createdDay;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=32, nullable=false)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="token_updated_at", type="datetime", nullable=false)
     */
    private $tokenUpdatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set userId
     *
     * @param integer $userId
     * @return GameSeekerDaily
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
     * Set points
     *
     * @param integer $points
     * @return GameSeekerDaily
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
     * Set createdDay
     *
     * @param \DateTime $createdDay
     * @return GameSeekerDaily
     */
    public function setCreatedDay($createdDay)
    {
        $this->createdDay = $createdDay;

        return $this;
    }

    /**
     * Get createdDay
     *
     * @return \DateTime 
     */
    public function getCreatedDay()
    {
        return $this->createdDay;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return GameSeekerDaily
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tokenUpdatedAt
     *
     * @param \DateTime $tokenUpdatedAt
     * @return GameSeekerDaily
     */
    public function setTokenUpdatedAt($tokenUpdatedAt)
    {
        $this->tokenUpdatedAt = $tokenUpdatedAt;

        return $this;
    }

    /**
     * Get tokenUpdatedAt
     *
     * @return \DateTime 
     */
    public function getTokenUpdatedAt()
    {
        return $this->tokenUpdatedAt;
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
