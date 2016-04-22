<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExchangeFlowOrder
 *
 * @ORM\Table(name="exchange_flow_order")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\ExchangeFlowOrderRepository")
 */
class ExchangeFlowOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", options={"comment": "USER ID"})
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="exchange_id", type="integer", nullable=true, options={"default": 0, "comment": "points_exchange.id"})
     */
    private $exchangeId;

    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=16, options={"comment":"手机号码所属运营商(移动、联通、电信)"})
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=64, options={"comment":"手机号码归属省份"})
     */
    private $province;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_product_id", type="string", length=5, options={"comment":"流量包产品编码"})
     */
    private $customProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="packagesize", type="string", length=8, options={"comment":"流量包产品大小，如30表示30MB"})
     */
    private $packagesize;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_prise", type="decimal", precision=8, scale=3, options={"comment":"流量包产品用户执行的价格，单位（元）"})
     */
    private $customPrise;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", options={"comment":""})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->exchangeId = 0;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return ExchangeFlowOrder
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
     * Set exchangeId
     *
     * @param integer $exchangeId
     * @return ExchangeFlowOrder
     */
    public function setExchangeId($exchangeId)
    {
        $this->exchangeId = $exchangeId;

        return $this;
    }

    /**
     * Get exchangeId
     *
     * @return integer
     */
    public function getExchangeId()
    {
        return $this->exchangeId;
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return ExchangeFlowOrder
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set province
     *
     * @param string $province
     * @return ExchangeFlowOrder
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set customProductId
     *
     * @param string $customProductId
     * @return ExchangeFlowOrder
     */
    public function setCustomProductId($customProductId)
    {
        $this->customProductId = $customProductId;

        return $this;
    }

    /**
     * Get customProductId
     *
     * @return string
     */
    public function getCustomProductId()
    {
        return $this->customProductId;
    }

    /**
     * Set packagesize
     *
     * @param string $packagesize
     * @return ExchangeFlowOrder
     */
    public function setPackagesize($packagesize)
    {
        $this->packagesize = $packagesize;

        return $this;
    }

    /**
     * Get packagesize
     *
     * @return string
     */
    public function getPackagesize()
    {
        return $this->packagesize;
    }

    /**
     * Set customPrise
     *
     * @param string $customPrise
     * @return ExchangeFlowOrder
     */
    public function setCustomPrise($customPrise)
    {
        $this->customPrise = $customPrise;

        return $this;
    }

    /**
     * Get customPrise
     *
     * @return string
     */
    public function getCustomPrise()
    {
        return $this->customPrise;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ExchangeFlowOrder
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ExchangeFlowOrder
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
