<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameEggsBreakerTaobaoOrder
 *
 * @ORM\Table(name="game_eggs_breaker_taobao_order", indexes={@ORM\Index(name="user_order", columns={"user_id", "order_id"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GameEggsBreakerTaobaoOrderRepository")
 */
class GameEggsBreakerTaobaoOrder
{
    const AUDIT_STATUS_INIT = 0;
    const AUDIT_STATUS_PENDING = 1;
    const AUDIT_STATUS_COMPLETED = 2;

    const ORDER_INIT = 0;
    const ORDER_VALID  = 1;
    const ORDER_INVALID  = 2;
    const ORDER_UNCERTAIN = 3;

    const IS_EGGED_INIT = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=255, nullable=false)
     */
    private $orderId;

    /**
     * @var float
     *
     * @ORM\Column(name="order_paid", type="float", precision=9, scale=2, nullable=false)
     */
    private $orderPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="audit_by", type="string", length=16, nullable=true)
     */
    private $auditBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="audit_status", type="integer", nullable=false)
     */
    private $auditStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="audit_pended_at", type="datetime", nullable=true)
     */
    private $auditPendedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_valid", type="integer", nullable=false)
     */
    private $isValid;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_egged", type="integer", nullable=false)
     */
    private $isEgged;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

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

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime())
            ->setAuditBy('')
            ->setAuditStatus(self::AUDIT_STATUS_INIT)
            ->setIsValid(self::ORDER_INIT)
            ->setIsEgged(self::IS_EGGED_INIT);
    }


    /**
     * Set userId
     *
     * @param integer $userId
     * @return GameEggsBreakerTaobaoOrder
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
     * Set orderId
     *
     * @param string $orderId
     * @return GameEggsBreakerTaobaoOrder
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set orderPaid
     *
     * @param float $orderPaid
     * @return GameEggsBreakerTaobaoOrder
     */
    public function setOrderPaid($orderPaid)
    {
        $this->orderPaid = $orderPaid;

        return $this;
    }

    /**
     * Get orderPaid
     *
     * @return float 
     */
    public function getOrderPaid()
    {
        return $this->orderPaid;
    }

    /**
     * Set auditBy
     *
     * @param string $auditBy
     * @return GameEggsBreakerTaobaoOrder
     */
    public function setAuditBy($auditBy)
    {
        $this->auditBy = $auditBy;

        return $this;
    }

    /**
     * Get auditBy
     *
     * @return string 
     */
    public function getAuditBy()
    {
        return $this->auditBy;
    }

    /**
     * Set auditStatus
     *
     * @param integer $auditStatus
     * @return GameEggsBreakerTaobaoOrder
     */
    public function setAuditStatus($auditStatus)
    {
        $this->auditStatus = $auditStatus;

        return $this;
    }

    /**
     * Get auditStatus
     *
     * @return integer 
     */
    public function getAuditStatus()
    {
        return $this->auditStatus;
    }

    /**
     * Set auditPendedAt
     *
     * @param \DateTime $auditPendedAt
     * @return GameEggsBreakerTaobaoOrder
     */
    public function setAuditPendedAt($auditPendedAt)
    {
        $this->auditPendedAt = $auditPendedAt;

        return $this;
    }

    /**
     * Get auditPendedAt
     *
     * @return \DateTime 
     */
    public function getAuditPendedAt()
    {
        return $this->auditPendedAt;
    }

    /**
     * Set isValid
     *
     * @param integer $isValid
     * @return GameEggsBreakerTaobaoOrder
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get isValid
     *
     * @return integer 
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set isEgged
     *
     * @param integer $isEgged
     * @return GameEggsBreakerTaobaoOrder
     */
    public function setIsEgged($isEgged)
    {
        $this->isEgged = $isEgged;

        return $this;
    }

    /**
     * Get isEgged
     *
     * @return integer 
     */
    public function getIsEgged()
    {
        return $this->isEgged;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return GameEggsBreakerTaobaoOrder
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return GameEggsBreakerTaobaoOrder
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
