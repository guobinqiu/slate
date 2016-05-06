<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarCommission
 *
 * @ORM\Table(name="emar_commission", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GeneralCommissionRepository")
 */
class EmarCommission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", nullable=false, options={"comment":"活动ID"})
     */
    protected $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="fixed_hash", type="string", length=64, nullable=false, options={"comment":"更新时使用"})
     */
    protected $fixedHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_activated", type="integer", nullable=false, options={"default":"0", "comment":"1: 使用中, 0: 不在使用"})
     */
    protected $isActivated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"comment":"写入时间"})
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
