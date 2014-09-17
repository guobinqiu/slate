<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEdmUnsubscribe
 *
 * @ORM\Table(name="user_edm_unsubscribe", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserEdmUnsubscribeRepository")
 */
class UserEdmUnsubscribe
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_time", type="datetime", nullable=true)
     */
    private $createdTime;

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
     * @return UserEdmUnsubscribe
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
     * Set createdTime
     *
     * @param \DateTime $createdTime
     * @return UserEdmUnsubscribe
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
