<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CpsAdvertisement
 *
 * @ORM\Table(name="cps_advertisement", uniqueConstraints={@ORM\UniqueConstraint(name="ad_id", columns={"ad_category_id", "ad_id", "is_activated"}), @ORM\UniqueConstraint(name="website_host", columns={"website_host", "is_activated"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\CpsAdvertisementRepository")
 */
class CpsAdvertisement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_category_id", type="integer", nullable=false)
     */
    private $adCategoryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", nullable=false)
     */
    private $adId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="marketing_url", type="text", nullable=false)
     */
    private $marketingUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_url", type="string", length=128, nullable=false)
     */
    private $adsUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="string", length=100, nullable=true)
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="website_name", type="string", length=64, nullable=false)
     */
    private $websiteName;

    /**
     * @var string
     *
     * @ORM\Column(name="website_category", type="string", length=128, nullable=false)
     */
    private $websiteCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="website_host", type="string", length=128, nullable=false)
     */
    private $websiteHost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="selected_at", type="datetime", nullable=true)
     */
    private $selectedAt;

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
     * Set adCategoryId
     *
     * @param integer $adCategoryId
     * @return CpsAdvertisement
     */
    public function setAdCategoryId($adCategoryId)
    {
        $this->adCategoryId = $adCategoryId;

        return $this;
    }

    /**
     * Get adCategoryId
     *
     * @return integer 
     */
    public function getAdCategoryId()
    {
        return $this->adCategoryId;
    }

    /**
     * Set adId
     *
     * @param integer $adId
     * @return CpsAdvertisement
     */
    public function setAdId($adId)
    {
        $this->adId = $adId;

        return $this;
    }

    /**
     * Get adId
     *
     * @return integer 
     */
    public function getAdId()
    {
        return $this->adId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return CpsAdvertisement
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set marketingUrl
     *
     * @param string $marketingUrl
     * @return CpsAdvertisement
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
     * Set adsUrl
     *
     * @param string $adsUrl
     * @return CpsAdvertisement
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
     * Set commission
     *
     * @param string $commission
     * @return CpsAdvertisement
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
     * Set websiteName
     *
     * @param string $websiteName
     * @return CpsAdvertisement
     */
    public function setWebsiteName($websiteName)
    {
        $this->websiteName = $websiteName;

        return $this;
    }

    /**
     * Get websiteName
     *
     * @return string 
     */
    public function getWebsiteName()
    {
        return $this->websiteName;
    }

    /**
     * Set websiteCategory
     *
     * @param string $websiteCategory
     * @return CpsAdvertisement
     */
    public function setWebsiteCategory($websiteCategory)
    {
        $this->websiteCategory = $websiteCategory;

        return $this;
    }

    /**
     * Get websiteCategory
     *
     * @return string 
     */
    public function getWebsiteCategory()
    {
        return $this->websiteCategory;
    }

    /**
     * Set websiteHost
     *
     * @param string $websiteHost
     * @return CpsAdvertisement
     */
    public function setWebsiteHost($websiteHost)
    {
        $this->websiteHost = $websiteHost;

        return $this;
    }

    /**
     * Get websiteHost
     *
     * @return string 
     */
    public function getWebsiteHost()
    {
        return $this->websiteHost;
    }

    /**
     * Set selectedAt
     *
     * @param \DateTime $selectedAt
     * @return CpsAdvertisement
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
     * Set isActivated
     *
     * @param integer $isActivated
     * @return CpsAdvertisement
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
