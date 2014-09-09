<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserConfigurations
 *
 * @ORM\Table(name="user_configurations", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_user_id1_flag_name1", columns={"user_id", "flag_name"})}, indexes={@ORM\Index(name="IDX_6899B580A76ED395", columns={"user_id"})})
 * @ORM\Entity
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
     * @var \Jili\FrontendBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Jili\FrontendBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



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
     * Set user
     *
     * @param \Jili\FrontendBundle\Entity\User $user
     * @return UserConfigurations
     */
    public function setUser(\Jili\FrontendBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Jili\FrontendBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
