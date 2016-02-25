<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CintPermission
 *
 * @ORM\Table(name="cint_permission")
 * @ORM\Entity
 */
class CintPermission
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="permission_flag", type="boolean", nullable=false)
     */
    private $permissionFlag;

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
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set permissionFlag
     *
     * @param boolean $permissionFlag
     * @return CintPermission
     */
    public function setPermissionFlag($permissionFlag)
    {
        $this->permissionFlag = $permissionFlag;

        return $this;
    }

    /**
     * Get permissionFlag
     *
     * @return boolean
     */
    public function getPermissionFlag()
    {
        return $this->permissionFlag;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return CintPermission
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
     * @return CintPermission
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
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
