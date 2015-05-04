<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChanetAdvertisement
 *
 * @ORM\Table(name="chanet_advertisement", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity
 */
class ChanetAdvertisement implements CustomRedirectUrlInterface
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
     * @ORM\Column(name="ads_url_type", type="string", length=128, nullable=false)
     */
    private $adsUrlType;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_url", type="string", length=128, nullable=false)
     */
    private $adsUrl;

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
     * @return ChanetAdvertisement
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
     * @return ChanetAdvertisement
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
     * @return ChanetAdvertisement
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
     * Set adsUrlType
     *
     * @param string $adsUrlType
     * @return ChanetAdvertisement
     */
    public function setAdsUrlType($adsUrlType)
    {
        $this->adsUrlType = $adsUrlType;

        return $this;
    }

    /**
     * Get adsUrlType
     *
     * @return string 
     */
    public function getAdsUrlType()
    {
        return $this->adsUrlType;
    }

    /**
     * Set adsUrl
     *
     * @param string $adsUrl
     * @return ChanetAdvertisement
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
     * Set marketingUrl
     *
     * @param string $marketingUrl
     * @return ChanetAdvertisement
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
     * @return ChanetAdvertisement
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
     * @return ChanetAdvertisement
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
     * @return ChanetAdvertisement
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

    /**
     *        chanet:
     *            e参数是为用户提供信息跟踪的功能，可利用此参数监测特定广告位等的广告效果。e参数仅支持不以0开头的数字，且最长为9位。 
     *            u参数是为用户提供信息跟踪的功能，可利用此参数监测特定广告位等的广告效果。u参数支持字符型，最长为255位。 
     *            http://count.chanet.com.cn/click.cgi?a=480534&d=375846&u=%E5%95%86%E5%AE%B6%E5%88%97%E8%A1%A8&e=999
     *
     */
    public function getRedirectUrlWithUserId($uid) 
    {
        $uri = $this->getMarketingUrl();
        if (strlen($uri) > 0 &&  1 ===  preg_match(  '/(^.*[\?&])e=(&?.*)$/', $uri, $matches)   ) {
            return $matches[1]. 'e='. $uid. $matches[2];
        };
        return '';
    }
}
