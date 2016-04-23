<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CpsAdvertisement
 *
 * @ORM\Table(name="cps_advertisement",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="ad_id", columns={"ad_category_id", "ad_id", "is_activated"}),
 *         @ORM\UniqueConstraint(name="website_host", columns={"website_host", "is_activated"})
 *     },
 *     indexes={
 *         @ORM\Index(name="website_name_dictionary_key", columns={"website_name_dictionary_key"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\CpsAdvertisementRepository")
 */
class CpsAdvertisement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_category_id", type="integer", options={"comment": "FK to ad_category"})
     */
    private $adCategoryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", options={"comment": "FK to XXX_advertisement"})
     */
    private $adId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, options={"comment": "活动名称"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="marketing_url", type="text", options={"comment": "推广链接,(cps平台的url)"})
     */
    private $marketingUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_url", type="string", length=128, options={"comment": "活动目标地址"})
     */
    private $adsUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="string", length=100, nullable=true, options={"comment": "返利详情", "default": ""})
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="website_name", type="string", length=64, options={"comment": "商家名称"})
     */
    private $websiteName;

    /**
     * @var string
     *
     * @ORM\Column(name="website_name_dictionary_key", type="string", length=1, options={"fixed":true, "comment": "商家名称索引", "default": ""})
     */
    private $websiteNameDictionaryKey;

    /**
     * @var string
     *
     * @ORM\Column(name="website_category", type="string", length=128, options={"comment": "活动分类"})
     */
    private $websiteCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="website_host", type="string", length=128, options={"comment": "活动地址(商家名)的域名，用于找logo"})
     */
    private $websiteHost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="selected_at", type="datetime", nullable=true, options={"comment": "选入cps_adver时间"})
     */
    private $selectedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_activated", type="integer", options={"comment": "1: 使用中, 0: 不在使用 , 2: 丢弃", "default": 0})
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
     * Set websiteNameDictionaryKey
     *
     * @param string $websiteNameDictionaryKey
     * @return CpsAdvertisement
     */
    public function setWebsiteNameDictionaryKey($websiteNameDictionaryKey)
    {
        $this->websiteNameDictionaryKey = $websiteNameDictionaryKey;

        return $this;
    }

    /**
     * Get websiteNameDictionaryKey
     *
     * @return string 
     */
    public function getWebsiteNameDictionaryKey()
    {
        return $this->websiteNameDictionaryKey;
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

    public function getHostHashAsLogoName()
    {
        return md5($this->getWebsiteHost());
    }
}
