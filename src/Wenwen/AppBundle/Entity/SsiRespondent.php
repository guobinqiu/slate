<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SsiRespondent
 *
 * @ORM\Table(name="ssi_respondent", uniqueConstraints={@ORM\UniqueConstraint(name="partner_app_member_id_UNIQUE", columns={"user_id"})})
 * @ORM\Entity
 */
class SsiRespondent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", options={"unsigned": true})
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_flag", type="smallint", nullable=true,
     *     options={"unsigned": true, "default": 1, "comment": "0:permission_no,1:permission_yes, 10:active"})
     */
    private $statusFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="stash_data", type="text", nullable=true)
     */
    private $stashData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
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

    /**
     * Set userId
     *
     * @param integer $userId
     * @return SsiRespondent
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
     * Set statusFlag
     *
     * @param integer $statusFlag
     * @return SsiRespondent
     */
    public function setStatusFlag($statusFlag)
    {
        $this->statusFlag = $statusFlag;

        return $this;
    }

    /**
     * Get statusFlag
     *
     * @return integer
     */
    public function getStatusFlag()
    {
        return $this->statusFlag;
    }

    /**
     * Set stashData
     *
     * @param string $stashData
     * @return SsiRespondent
     */
    public function setStashData($stashData)
    {
        $this->stashData = $stashData;

        return $this;
    }

    /**
     * Get stashData
     *
     * @return string
     */
    public function getStashData()
    {
        return $this->stashData;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return SsiRespondent
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
     * @return SsiRespondent
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
}