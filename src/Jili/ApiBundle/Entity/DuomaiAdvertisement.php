<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DuomaiAdvertisement
 *
 * @ORM\Table(name="duomai_advertisement", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity
 */
class DuomaiAdvertisement
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
     * @ORM\Column(name="ads_url", type="string", length=128, nullable=false)
     */
    private $adsUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_commission", type="string", length=64, nullable=false)
     */
    private $adsCommission;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="date", nullable=false)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="date", nullable=false)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=128, nullable=false)
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="return_day", type="integer", nullable=false)
     */
    private $returnDay;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_cycle", type="string", length=64, nullable=false)
     */
    private $billingCycle;

    /**
     * @var string
     *
     * @ORM\Column(name="link_custom", type="string", length=128, nullable=false)
     */
    private $linkCustom;

    /**
     * @var string
     *
     * @ORM\Column(name="link_custom_short", type="string", length=128, nullable=false)
     */
    private $linkCustomShort;

    /**
     * @var string
     *
     * @ORM\Column(name="fixed_hash", type="string", length=64, nullable=false)
     */
    private $fixedHash;

    /**
     * @var string
     *
     * @ORM\Column(name="is_activated", type="string", length=32, nullable=false)
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
     * @return DuomaiAdvertisement
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
     * @return DuomaiAdvertisement
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
     * Set adsUrl
     *
     * @param string $adsUrl
     * @return DuomaiAdvertisement
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
     * Set adsCommission
     *
     * @param string $adsCommission
     * @return DuomaiAdvertisement
     */
    public function setAdsCommission($adsCommission)
    {
        $this->adsCommission = $adsCommission;

        return $this;
    }

    /**
     * Get adsCommission
     *
     * @return string 
     */
    public function getAdsCommission()
    {
        return $this->adsCommission;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return DuomaiAdvertisement
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return DuomaiAdvertisement
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return DuomaiAdvertisement
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
     * Set returnDay
     *
     * @param integer $returnDay
     * @return DuomaiAdvertisement
     */
    public function setReturnDay($returnDay)
    {
        $this->returnDay = $returnDay;

        return $this;
    }

    /**
     * Get returnDay
     *
     * @return integer 
     */
    public function getReturnDay()
    {
        return $this->returnDay;
    }

    /**
     * Set billingCycle
     *
     * @param string $billingCycle
     * @return DuomaiAdvertisement
     */
    public function setBillingCycle($billingCycle)
    {
        $this->billingCycle = $billingCycle;

        return $this;
    }

    /**
     * Get billingCycle
     *
     * @return string 
     */
    public function getBillingCycle()
    {
        return $this->billingCycle;
    }

    /**
     * Set linkCustom
     *
     * @param string $linkCustom
     * @return DuomaiAdvertisement
     */
    public function setLinkCustom($linkCustom)
    {
        $this->linkCustom = $linkCustom;

        return $this;
    }

    /**
     * Get linkCustom
     *
     * @return string 
     */
    public function getLinkCustom()
    {
        return $this->linkCustom;
    }

    /**
     * Set linkCustomShort
     *
     * @param string $linkCustomShort
     * @return DuomaiAdvertisement
     */
    public function setLinkCustomShort($linkCustomShort)
    {
        $this->linkCustomShort = $linkCustomShort;

        return $this;
    }

    /**
     * Get linkCustomShort
     *
     * @return string 
     */
    public function getLinkCustomShort()
    {
        return $this->linkCustomShort;
    }

    /**
     * Set fixedHash
     *
     * @param string $fixedHash
     * @return DuomaiAdvertisement
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
     * @param string $isActivated
     * @return DuomaiAdvertisement
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * Get isActivated
     *
     * @return string 
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
