<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarAdvertisement
 *
 * @ORM\Table(name="emar_advertisement", uniqueConstraints={@ORM\UniqueConstraint(name="fixed_hash", columns={"fixed_hash"})})
 * @ORM\Entity
 */
class EmarAdvertisement implements CustomRedirectUrlInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", nullable=false, options={"comment":"活动ID"})
     */
    private $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_name", type="string", length=64, nullable=false, options={"comment":"活动名称"})
     */
    private $adsName;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=128, nullable=false, options={"default":"", "comment":"活动分类"})
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="string", length=128, nullable=false, options={"default":"", "comment":"佣金"})
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_period", type="string", length=100, nullable=true, options={"default":"", "comment":"结算周期"})
     */
    private $commissionPeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_url", type="string", length=128, nullable=false, options={"comment":"首页地址"})
     */
    private $adsUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_customize_target", type="integer", nullable=false, options={"default":1, "comment":"是否允许修改目标地址"})
     */
    private $canCustomizeTarget;

    /**
     * @var string
     *
     * @ORM\Column(name="feedback_tag", type="string", length=4, nullable=false, options={"default":"c", "comment":"反馈标签"})
     */
    private $feedbackTag;

    /**
     * @var string
     *
     * @ORM\Column(name="marketing_url", type="text", nullable=false, options={"comment":"自定义链接"})
     */
    private $marketingUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="selected_at", type="datetime", nullable=true, options={"default":NULL, "comment":"选入cps_adver时间"})
     */
    private $selectedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="fixed_hash", type="string", length=64, nullable=false, options={"comment":"更新时使用"})
     */
    private $fixedHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_activated", type="integer", nullable=false, options={"default":0, "comment":"1: 使用中, 0: 不在使用"})
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

    /**
     *        yiqifa:
     *            http://p.yiqifa.com/n?k=2mLErnDS6ERLrI6H2mLErI6HWNzqWn4H6ljFWnzernKlWlRs1QLErZyH2mq96OW76EjsWcqtCwAMWOgH&e=APIMemberId&t=http://www.peteralexander.com.au/shop/en/peteralexander
     *
     *            反馈用标签(eu_id)是用来跟踪联盟会员网站中自己的会员进行购物活动的一个标识。
     *            反馈标签是根据联盟会员网站建站技术来确定其格式的，一般情况下可以为空。 
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
