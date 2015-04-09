<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DuomaiOrder
 *
 * @ORM\Table(name="duomai_order", uniqueConstraints={@ORM\UniqueConstraint(name="ocd", columns={"ocd"})})
 * @ORM\Entity
 */
class DuomaiOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ads_id", type="integer", nullable=false)
     */
    private $adsId;

    /**
     * @var string
     *
     * @ORM\Column(name="ads_name", type="string", length=128, nullable=false)
     */
    private $adsName;

    /**
     * @var integer
     *
     * @ORM\Column(name="site_id", type="integer", nullable=false)
     */
    private $siteId;

    /**
     * @var integer
     *
     * @ORM\Column(name="link_id", type="integer", nullable=false)
     */
    private $linkId;

    /**
     * @var string
     *
     * @ORM\Column(name="ocd", type="string", length=100, nullable=false)
     */
    private $ocd;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_time", type="integer", nullable=false)
     */
    private $orderTime;

    /**
     * @var float
     *
     * @ORM\Column(name="orders_price", type="float", precision=10, scale=2, nullable=false)
     */
    private $ordersPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="comm", type="float", precision=10, scale=2, nullable=false)
     */
    private $comm;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deactivated_at", type="datetime", nullable=false)
     */
    private $deactivatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=false)
     */
    private $confirmedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="balanced_at", type="datetime", nullable=false)
     */
    private $balancedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
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
     * Set orderTime
     *
     * @param integer $orderTime
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
     * @return integer 
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
}
