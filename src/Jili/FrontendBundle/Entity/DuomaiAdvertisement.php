<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DuomaiAdvertisement
 *
 * @ORM\Table(name="duomai_advertisement", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity
 */
class DuomaiAdvertisement implements CustomRedirectUrlInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", options={"comment": "活动ID"})
     */
    private $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_name", type="string", length=64, options={"comment": "活动名称"})
     */
    private $adsName;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_url", type="string", length=128, options={"comment": "网址"})
     */
    private $adsUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_commission", type="string", length=64, options={"comment": "佣金"})
     */
    private $adsCommission;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="date", options={"comment": "活动时间(起)"})
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="date", options={"comment": "活动时间(止)"})
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=128, options={"comment": "活动分类"})
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="return_day", type="integer", options={"comment": "效果认定期RD", "default": 0})
     */
    private $returnDay;

    /**
     * @var string
     *
     * @ORM\Column(name="billing_cycle", type="string", length=255, nullable=false, options={"comment": "结算周期"})
     */
    private $billingCycle;

    /**
     * @var string
     *
     * @ORM\Column(name="link_custom", type="string", length=128, nullable=false, options={"comment": "自定义链接"})
     */
    private $linkCustom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="selected_at", type="datetime", nullable=true, options={"comment": "选入cps_adver时间"})
     */
    private $selectedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="fixed_hash", type="string", length=64, nullable=false, options={"comment": "更新时使用"})
     */
    private $fixedHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_activated", type="integer", nullable=false, options={"comment": "1: 使用中, 0: 不在使用", "default": 0})
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
     * Set selectedAt
     *
     * @param \DateTime $selectedAt
     * @return DuomaiAdvertisement
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
     * @param integer $isActivated
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
     *   duomai:
     *       http://c.duomai.com/track.php?site_id=152244&aid=57&euid=APIMemberId&t=http%3A%2F%2Fwww.mbaobao.com%2F
     */
    public function getRedirectUrlWithUserId($uid) 
    {
        $uri = $this->getLinkCustom();
        if (strlen($uri) > 0 &&  1 ===  preg_match(  '/(^.*[\?&])euid=(&?.*)$/', $uri, $matches)   ) {
            return $matches[1]. 'euid='. $uid. $matches[2];
        };
        return '';
    }
}
