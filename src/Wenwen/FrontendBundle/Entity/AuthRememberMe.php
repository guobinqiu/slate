<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AuthRememberMe
 *
 * @ORM\Table(
        name="auth_remember_me",
        uniqueConstraints={
            @ORM\UniqueConstraint(columns={"user_id"}),
            @ORM\UniqueConstraint(columns={"token"})
            }
        )
 * @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\AuthRememberMeRepository")
 */
class AuthRememberMe
{
    const EXPIRE_DURATION = '+ 15 days';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * token by email auth
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=false)
     */
    private $token;

    /**
     * Token will be expired after expiredAt.
     * @ORM\Column(name="expired_at", type="datetime", nullable=false)
     */
    private $expiredAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


    public function __construct()
    {
        $currentTime = new \DateTime();
        $expiredAt = new \DateTime(self::EXPIRE_DURATION);
        $this->createdAt = $currentTime;
        $this->updatedAt = $currentTime;
        $this->expiredAt = $expiredAt;
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

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return AuthEmail
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
     * Set expiredAt
     *
     * @param \DateTime $expiredAt
     * @return AuthEmail
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * Get expiredAt
     *
     * @return \DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * token是否已过期
     */
    public function isTokenExpired()
    {
        return new \DateTime() > $this->expiredAt;
    }
}
