<?php
namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserConfigurations
 *
 * @ORM\Table(name="user_configurations", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_user_id1_flag_name1", columns={"user_id", "flag_name"})}, indexes={@ORM\Index(name="IDX_6899B580A76ED395", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserConfigurationsRepository")
 */
class UserConfigurations
{
    /**
     * @var string
     *
     * @ORM\Column(name="flag_name", type="string", length=64, nullable=false)
     */
    private $flagName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="flag_data", type="boolean", nullable=true)
     */
    private $flagData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

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

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    public function __construct() {
        $this->setCreatedAt( new \Datetime() );
        $this->setUpdatedAt( new \Datetime() );
    }

    /**
     * Set flagName
     *
     * @param string $flagName
     * @return UserConfigurations
     */
    public function setFlagName($flagName)
    {
        $this->flagName = $flagName;

        return $this;
    }

    /**
     * Get flagName
     *
     * @return string
     */
    public function getFlagName()
    {
        return $this->flagName;
    }

    /**
     * Set flagData
     *
     * @param boolean $flagData
     * @return UserConfigurations
     */
    public function setFlagData($flagData)
    {
        $this->flagData = $flagData;

        return $this;
    }

    /**
     * Get flagData
     *
     * @return boolean
     */
    public function getFlagData()
    {
        return $this->flagData;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return UserConfigurations
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
     * @return UserConfigurations
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

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserConfigurations
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
