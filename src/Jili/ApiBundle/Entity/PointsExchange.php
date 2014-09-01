<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointsExchange
 *
 * @ORM\Table(name="points_exchange")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\PointsExchangeRepository")
 */
class PointsExchange
{

    public function __construct()
    {
        $this->exchangeDate = new \DateTime();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="exchange_date", type="datetime")
     */
    private $exchangeDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finish_date", type="datetime", nullable=true)
     */
    private $finishDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="target_account", type="string", length=45)
     */
    private $targetAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="real_name", type="string", length=45, nullable=true)
     */
    private $realName;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_point", type="integer")
     */
    private $sourcePoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_point", type="integer")
     */
    private $targetPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="exchange_item_number", type="integer")
     */
    private $exchangeItemNumber;
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=20)
     */
    private $ip;



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
     * Set userId
     *
     * @param integer $userId
     * @return PointsExchange
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
     * Set exchangeDate
     *
     * @param \DateTime $exchangeDate
     * @return PointsExchange
     */
    public function setExchangeDate($exchangeDate)
    {
        $this->exchangeDate = $exchangeDate;

        return $this;
    }

    /**
     * Get exchangeDate
     *
     * @return \DateTime
     */
    public function getExchangeDate()
    {
        return $this->exchangeDate;
    }

    /**
     * Set finishDate
     *
     * @param \DateTime $finishDate
     * @return PointsExchange
     */
    public function setFinishDate($finishDate)
    {
        $this->finishDate = $finishDate;

        return $this;
    }

    /**
     * Get finishDate
     *
     * @return \DateTime
     */
    public function getFinishDate()
    {
        return $this->finishDate;
    }



    /**
     * Set type
     *
     * @param integer $type
     * @return PointsExchange
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set targetAccount
     *
     * @param string $targetAccount
     * @return PointsExchange
     */
    public function setTargetAccount($targetAccount)
    {
        $this->targetAccount = $targetAccount;

        return $this;
    }

    /**
     * Get targetAccount
     *
     * @return string
     */
    public function getTargetAccount()
    {
        return $this->targetAccount;
    }


    /**
     * Set realName
     *
     * @param string $realName
     * @return PointsExchange
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;

        return $this;
    }

    /**
     * Get realName
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Set sourcePoint
     *
     * @param integer $point
     * @return PointsExchange
     */
    public function setSourcePoint($sourcePoint)
    {
        $this->sourcePoint = $sourcePoint;

        return $this;
    }

    /**
     * Get sourcePoint
     *
     * @return integer
     */
    public function getSourcePoint()
    {
        return $this->sourcePoint;
    }

    /**
     * Set targetPoint
     *
     * @param integer $targetPoint
     * @return PointsExchange
     */
    public function setTargetPoint($targetPoint)
    {
        $this->targetPoint = $targetPoint;

        return $this;
    }

    /**
     * Get targetPoint
     *
     * @return integer
     */
    public function getTargetPoint()
    {
        return $this->targetPoint;
    }


     /**
     * Set exchangeItemNumber
     *
     * @param integer $exchangeItemNumber
     * @return PointsExchange
     */
    public function setExchangeItemNumber($exchangeItemNumber)
    {
        $this->exchangeItemNumber = $exchangeItemNumber;

        return $this;
    }

    /**
     * Get exchangeItemNumber
     *
     * @return integer
     */
    public function getExchangeItemNumber()
    {
        return $this->exchangeItemNumber;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return PointsExchange
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
     * Set ip
     *
     * @param string $ip
     * @return PointsExchange
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
}
