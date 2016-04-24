<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserWenwenCrossToken
 *
 * @ORM\Table(name="user_wenwen_cross_token", uniqueConstraints={@ORM\UniqueConstraint(name="cross_id", columns={"cross_id"}), @ORM\UniqueConstraint(name="token", columns={"token"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserWenwenCrossTokenRepository")
 */
class UserWenwenCrossToken
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cross_id", type="integer")
     */
    private $crossId;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64)
     */
    private $token;

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

    public function __construct()
    {
        $this->setCreatedAt( new \Datetime() );
    }

    /**
     * Set crossId
     *
     * @param integer $crossId
     * @return UserWenwenCrossToken
     */
    public function setCrossId($crossId)
    {
        $this->crossId = $crossId;

        return $this;
    }

    /**
     * Get crossId
     *
     * @return integer
     */
    public function getCrossId()
    {
        return $this->crossId;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return UserWenwenCrossToken
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserWenwenCrossToken
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
