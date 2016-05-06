<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SopProfilePoint
 *
 * @ORM\Table(name="sop_profile_point",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="hash_uniq", columns={"hash"})
 *     },
 *     indexes={
 *         @ORM\Index(name="panelist_status_idx", columns={"status_flag", "user_id"}),
 *         @ORM\Index(name="sop_status_idx", columns={"status_flag", "id"}),
 *         @ORM\Index(name="updated_at_idx", columns={"updated_at"}),
 *         @ORM\Index(name="name_user_idx", columns={"name", "user_id"})
 *     }
 * )
 * @ORM\Entity
 */
class SopProfilePoint
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=16, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="point_value", type="integer", options={"default": 0})
     */
    private $pointValue;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status_flag", type="boolean", nullable=true, options={"default": 1})
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

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setPointValue(0);
        $this->setStatusFlag(1);
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return SopProfilePoint
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
     * Set name
     *
     * @param string $name
     * @return SopProfilePoint
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set pointValue
     *
     * @param integer $pointValue
     * @return SopProfilePoint
     */
    public function setPointValue($pointValue)
    {
        $this->pointValue = $pointValue;

        return $this;
    }

    /**
     * Get pointValue
     *
     * @return integer
     */
    public function getPointValue()
    {
        return $this->pointValue;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return SopProfilePoint
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set statusFlag
     *
     * @param boolean $statusFlag
     * @return SopProfilePoint
     */
    public function setStatusFlag($statusFlag)
    {
        $this->statusFlag = $statusFlag;

        return $this;
    }

    /**
     * Get statusFlag
     *
     * @return boolean
     */
    public function getStatusFlag()
    {
        return $this->statusFlag;
    }

    /**
     * Set stashData
     *
     * @param string $stashData
     * @return SopProfilePoint
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
     * @return SopProfilePoint
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
     * @return SopProfilePoint
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
