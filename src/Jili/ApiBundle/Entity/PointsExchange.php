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
     * @ORM\Column(name="exchange_date", type="datetime", nullable=true)
     */
    private $exchangeDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="account", type="string", length=45, nullable=true)
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="point", type="string", length=45, nullable=true)
     */
    private $point;

    /**
     * @var string
     *
     * @ORM\Column(name="exchanged_point", type="string", length=45, nullable=true)
     */
    private $exchangedPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
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
     * Set account
     *
     * @param string $account
     * @return PointsExchange
     */
    public function setAccount($account)
    {
        $this->account = $account;
    
        return $this;
    }

    /**
     * Get account
     *
     * @return string 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set point
     *
     * @param string $point
     * @return PointsExchange
     */
    public function setPoint($point)
    {
        $this->point = $point;
    
        return $this;
    }

    /**
     * Get point
     *
     * @return string 
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set exchangedPoint
     *
     * @param string $exchangedPoint
     * @return PointsExchange
     */
    public function setExchangedPoint($exchangedPoint)
    {
        $this->exchangedPoint = $exchangedPoint;
    
        return $this;
    }

    /**
     * Get exchangedPoint
     *
     * @return string 
     */
    public function getExchangedPoint()
    {
        return $this->exchangedPoint;
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