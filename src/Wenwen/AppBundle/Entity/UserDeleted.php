<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserDeleted
 *
 * @ORM\Table(name="user_deleted")
 * @ORM\Entity
 */
class UserDeleted
{
    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text", nullable=true)
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="user_info", type="text", nullable=true)
     */
    private $userInfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return UserDeleted
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set userInfo
     *
     * @param string $userInfo
     * @return UserDeleted
     */
    public function setUserInfo($userInfo)
    {
        $this->userInfo = $userInfo;

        return $this;
    }

    /**
     * Get userInfo
     *
     * @return string
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserDeleted
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
     * Set userId
     *
     * @param integer $userId
     * @return UserDeleted
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
}
