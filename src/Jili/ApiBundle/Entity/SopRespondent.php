<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * SopRespondent
 *
 * @ORM\Table(name="sop_respondent",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="user_id_idx", columns={"user_id"}),
 *         @ORM\UniqueConstraint(name="app_mid_idx", columns={"app_mid"}),
 *     },
 *     indexes={
 *         @ORM\Index(name="user_status_idx", columns={"status_flag", "user_id"}),
 *         @ORM\Index(name="sop_status_idx", columns={"status_flag", "id"}),
 *         @ORM\Index(name="updated_at_idx", columns={"updated_at"}),
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\SopRespondentRepository")
 */
class SopRespondent
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

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

    /**
     * @var string
     *
     * @ORM\Column(name="app_mid", type="string", nullable=false)
     *
     * @link https://stackoverflow.com/questions/20342058/which-uuid-version-to-use
     */
    private $appMid;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setStatusFlag(self::STATUS_ACTIVE);
        $this->setAppMid(Uuid::uuid1()->toString());
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return SopRespondent
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
     * @param boolean $statusFlag
     * @return SopRespondent
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
     * @return SopRespondent
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
     * @return SopRespondent
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
     * @return SopRespondent
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

    public function setAppMid($appMid) {
        $this->appMid = $appMid;
        return $this;
    }

    public function getAppMid() {
        return $this->appMid;
    }
}
