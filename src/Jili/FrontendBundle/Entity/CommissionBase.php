<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommissionBase
 *
 * @ORM\MappedSuperclass 
 */
class CommissionBase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", nullable=false)
     */
    protected $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="fixed_hash", type="string", length=64, nullable=false)
     */
    protected $fixedHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_activated", type="integer", nullable=false)
     */
    protected $isActivated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;



    /**
     * Set adsId
     *
     * @param integer $adsId
     * @return ChanetCommission
     */
    public function setAdsId($adsId)
    {
        $this->adsId = $adsId;

        return $this;
    }

    /**
     * Get adsId
     *
     * @return integer 
     */
    public function getAdsId()
    {
        return $this->adsId;
    }

    /**
     * Set fixedHash
     *
     * @param string $fixedHash
     * @return ChanetCommission
     */
    public function setFixedHash($fixedHash)
    {
        $this->fixedHash = $fixedHash;

        return $this;
    }

    /**
     * Get fixedHash
     *
     * @return string 
     */
    public function getFixedHash()
    {
        return $this->fixedHash;
    }

    /**
     * Set isActivated
     *
     * @param integer $isActivated
     * @return ChanetCommission
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * Get isActivated
     *
     * @return integer 
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChanetCommission
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
