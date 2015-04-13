<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarAdvertisement
 *
 * @ORM\Table(name="emar_advertisement", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity
 */
class EmarAdvertisement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", nullable=false)
     */
    private $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_name", type="string", length=64, nullable=false)
     */
    private $adsName;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=128, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="string", length=128, nullable=false)
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_period", type="string", length=100, nullable=true)
     */
    private $commissionPeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_url", type="string", length=128, nullable=false)
     */
    private $adsUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_customize_target", type="integer", nullable=false)
     */
    private $canCustomizeTarget;

    /**
     * @var string
     *
     * @ORM\Column(name="feedback_tag", type="string", length=4, nullable=false)
     */
    private $feedbackTag;

    /**
     * @var string
     *
     * @ORM\Column(name="marketing_url", type="text", nullable=false)
     */
    private $marketingUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="selected_at", type="datetime", nullable=true)
     */
    private $selectedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="fixed_hash", type="string", length=64, nullable=false)
     */
    private $fixedHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_activated", type="integer", nullable=false)
     */
    private $isActivated;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set adsId
     *
     * @param integer $adsId
     * @return EmarAdvertisement
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
     * Set adsName
     *
     * @param string $adsName
     * @return EmarAdvertisement
     */
    public function setAdsName($adsName)
    {
        $this->adsName = $adsName;

        return $this;
    }

    /**
     * Get adsName
     *
     * @return string 
     */
    public function getAdsName()
    {
        return $this->adsName;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return EmarAdvertisement
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set commission
     *
     * @param string $commission
     * @return EmarAdvertisement
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return string 
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set commissionPeriod
     *
     * @param string $commissionPeriod
     * @return EmarAdvertisement
     */
    public function setCommissionPeriod($commissionPeriod)
    {
        $this->commissionPeriod = $commissionPeriod;

        return $this;
    }

    /**
     * Get commissionPeriod
     *
     * @return string 
     */
    public function getCommissionPeriod()
    {
        return $this->commissionPeriod;
    }

    /**
     * Set adsUrl
     *
     * @param string $adsUrl
     * @return EmarAdvertisement
     */
    public function setAdsUrl($adsUrl)
    {
        $this->adsUrl = $adsUrl;

        return $this;
    }

    /**
     * Get adsUrl
     *
     * @return string 
     */
    public function getAdsUrl()
    {
        return $this->adsUrl;
    }

    /**
     * Set canCustomizeTarget
     *
     * @param integer $canCustomizeTarget
     * @return EmarAdvertisement
     */
    public function setCanCustomizeTarget($canCustomizeTarget)
    {
        $this->canCustomizeTarget = $canCustomizeTarget;

        return $this;
    }

    /**
     * Get canCustomizeTarget
     *
     * @return integer 
     */
    public function getCanCustomizeTarget()
    {
        return $this->canCustomizeTarget;
    }

    /**
     * Set feedbackTag
     *
     * @param string $feedbackTag
     * @return EmarAdvertisement
     */
    public function setFeedbackTag($feedbackTag)
    {
        $this->feedbackTag = $feedbackTag;

        return $this;
    }

    /**
     * Get feedbackTag
     *
     * @return string 
     */
    public function getFeedbackTag()
    {
        return $this->feedbackTag;
    }

    /**
     * Set marketingUrl
     *
     * @param string $marketingUrl
     * @return EmarAdvertisement
     */
    public function setMarketingUrl($marketingUrl)
    {
        $this->marketingUrl = $marketingUrl;

        return $this;
    }

    /**
     * Get marketingUrl
     *
     * @return string 
     */
    public function getMarketingUrl()
    {
        return $this->marketingUrl;
    }

    /**
     * Set selectedAt
     *
     * @param \DateTime $selectedAt
     * @return EmarAdvertisement
     */
    public function setSelectedAt($selectedAt)
    {
        $this->selectedAt = $selectedAt;

        return $this;
    }

    /**
     * Get selectedAt
     *
     * @return \DateTime 
     */
    public function getSelectedAt()
    {
        return $this->selectedAt;
    }

    /**
     * Set fixedHash
     *
     * @param string $fixedHash
     * @return EmarAdvertisement
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
     * @return EmarAdvertisement
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
