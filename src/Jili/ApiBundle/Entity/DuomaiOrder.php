<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jili\ApiBundle\Component\OrderBase;

/**
 * DuomaiOrder
 *
 * @ORM\Table(name="duomai_order", uniqueConstraints={@ORM\UniqueConstraint(name="order_idx", columns={"site_id", "ocd"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\DuomaiOrderRepository")
 */
class DuomaiOrder
{


    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", options={"comment": "euid网站主设定的反馈标签"})
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="ocd", type="string", length=32, options={"comment": "请求参数中的id"})
     */
    private $ocd;

    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", options={"comment": "活动ID"})
     */
    private $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_name", type="string", length=128, options={"comment": "活动名称"})
     */
    private $adsName;

    /**
     * @var integer
     *
     * @ORM\Column(name="site_id", type="integer", options={"comment": "网站ID"})
     */
    private $siteId;

    /**
     * @var integer
     *
     * @ORM\Column(name="link_id", type="integer", options={"comment": "活动链接ID"})
     */
    private $linkId;

    /**
     * @var string
     *
     * @ORM\Column(name="order_sn", type="string", length=32, options={"comment": "order_sn 订单编号"})
     */
    private $orderSn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_time", type="datetime", options={"comment": "下单时间", "default": "0000-00-00 00:00:00"})
     */
    private $orderTime;

    /**
     * @var float
     *
     * @ORM\Column(name="orders_price", type="float", nullable=true, options={"default": 0, "comment": "订单金额"})
     */
    private $ordersPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="comm", type="float", nullable=true, options={"default": 0, "comment": "siter_commission 订单佣金"})
     */
    private $comm;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true, options={"default": 0, "comment": "订单状态  -1 无效 0 未确认 1 确认 2 结算"})
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deactivated_at", type="datetime", nullable=true, options={"comment": "status= -1 的时间", "default": "0000-00-00 00:00:00"})
     */
    private $deactivatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true, options={"comment": "status= 1 的时间", "default": "0000-00-00 00:00:00"})
     */
    private $confirmedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="balanced_at", type="datetime", nullable=true, options={"comment": "status= 2 的时间", "default": "0000-00-00 00:00:00"})
     */
    private $balancedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment": "status= 0 的时间", "default": "0000-00-00 00:00:00"})
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
     * Set userId
     *
     * @param integer $userId
     * @return DuomaiOrder
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set ocd
     *
     * @param string $ocd
     * @return DuomaiOrder
     */
    public function setOcd($ocd)
    {
        $this->ocd = $ocd;

        return $this;
    }

    /**
     * Get ocd
     *
     * @return string 
     */
    public function getOcd()
    {
        return $this->ocd;
    }

    /**
     * Set adsId
     *
     * @param integer $adsId
     * @return DuomaiOrder
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
     * @return DuomaiOrder
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
     * Set siteId
     *
     * @param integer $siteId
     * @return DuomaiOrder
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * Get siteId
     *
     * @return integer 
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Set linkId
     *
     * @param integer $linkId
     * @return DuomaiOrder
     */
    public function setLinkId($linkId)
    {
        $this->linkId = $linkId;

        return $this;
    }

    /**
     * Get linkId
     *
     * @return integer 
     */
    public function getLinkId()
    {
        return $this->linkId;
    }

    /**
     * Set orderSn
     *
     * @param string $orderSn
     * @return DuomaiOrder
     */
    public function setOrderSn($orderSn)
    {
        $this->orderSn = $orderSn;

        return $this;
    }

    /**
     * Get orderSn
     *
     * @return string 
     */
    public function getOrderSn()
    {
        return $this->orderSn;
    }

    /**
     * Set orderTime
     *
     * @param \DateTime $orderTime
     * @return DuomaiOrder
     */
    public function setOrderTime($orderTime)
    {
        $this->orderTime = $orderTime;

        return $this;
    }

    /**
     * Get orderTime
     *
     * @return \DateTime 
     */
    public function getOrderTime()
    {
        return $this->orderTime;
    }

    /**
     * Set ordersPrice
     *
     * @param float $ordersPrice
     * @return DuomaiOrder
     */
    public function setOrdersPrice($ordersPrice)
    {
        $this->ordersPrice = $ordersPrice;

        return $this;
    }

    /**
     * Get ordersPrice
     *
     * @return float 
     */
    public function getOrdersPrice()
    {
        return $this->ordersPrice;
    }

    /**
     * Set comm
     *
     * @param float $comm
     * @return DuomaiOrder
     */
    public function setComm($comm)
    {
        $this->comm = $comm;

        return $this;
    }

    /**
     * Get comm
     *
     * @return float 
     */
    public function getComm()
    {
        return $this->comm;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return DuomaiOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deactivatedAt
     *
     * @param \DateTime $deactivatedAt
     * @return DuomaiOrder
     */
    public function setDeactivatedAt($deactivatedAt)
    {
        $this->deactivatedAt = $deactivatedAt;

        return $this;
    }

    /**
     * Get deactivatedAt
     *
     * @return \DateTime 
     */
    public function getDeactivatedAt()
    {
        return $this->deactivatedAt;
    }

    /**
     * Set confirmedAt
     *
     * @param \DateTime $confirmedAt
     * @return DuomaiOrder
     */
    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    /**
     * Get confirmedAt
     *
     * @return \DateTime 
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * Set balancedAt
     *
     * @param \DateTime $balancedAt
     * @return DuomaiOrder
     */
    public function setBalancedAt($balancedAt)
    {
        $this->balancedAt = $balancedAt;

        return $this;
    }

    /**
     * Get balancedAt
     *
     * @return \DateTime 
     */
    public function getBalancedAt()
    {
        return $this->balancedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return DuomaiOrder
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

    public function __construct() {
        $ds = new \DateTime();
        $era  = new \DateTime();
        $era->setTimestamp(0);

        $this->setComm(0)
            ->setStatus( OrderBase::getInitStatus() )
            ->setDeactivatedAt($era)
            ->setConfirmedAt($era)
            ->setBalancedAt($era)
            ->setCreatedAt($ds);
    }

    public function isPending() {
        return $this->getStatus() === OrderBase::getInitStatus() ; 
    }

    public function isConfirmed() {
        return $this->getStatus() === OrderBase::getPendingStatus();

    }

    public function isInvalid() {
        return $this->getStatus() === OrderBase::getSuccessStatus();
    }

    public function isBalanced() {
        return $this->getStatus() === OrderBase::getFailedStatus();
    }

}
