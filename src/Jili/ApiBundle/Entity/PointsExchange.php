<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointsExchange
 */
class PointsExchange
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var \DateTime
     */
    private $exchange_date;

    /**
     * @var int
     */
    private $type;

    /**
     * @var varchar
     */
    private $account;

    /**
     * @var varchar
     */
    private $point;

    /**
     * @var int
     */
    private $status;

    /**
     * @var varchar
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
     * Set user_id
     *
     * @param \int $userId
     * @return PointsExchange
     */
    public function setUserId(\int $userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return \int 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set exchange_date
     *
     * @param \DateTime $exchangeDate
     * @return PointsExchange
     */
    public function setExchangeDate($exchangeDate)
    {
        $this->exchange_date = $exchangeDate;
    
        return $this;
    }

    /**
     * Get exchange_date
     *
     * @return \DateTime 
     */
    public function getExchangeDate()
    {
        return $this->exchange_date;
    }

    /**
     * Set type
     *
     * @param \int $type
     * @return PointsExchange
     */
    public function setType(\int $type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \int 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set account
     *
     * @param \varchar $account
     * @return PointsExchange
     */
    public function setAccount(\varchar $account)
    {
        $this->account = $account;
    
        return $this;
    }

    /**
     * Get account
     *
     * @return \varchar 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set point
     *
     * @param \varchar $point
     * @return PointsExchange
     */
    public function setPoint(\varchar $point)
    {
        $this->point = $point;
    
        return $this;
    }

    /**
     * Get point
     *
     * @return \varchar 
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set status
     *
     * @param \int $status
     * @return PointsExchange
     */
    public function setStatus(\int $status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return \int 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set ip
     *
     * @param \varchar $ip
     * @return PointsExchange
     */
    public function setIp(\varchar $ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return \varchar 
     */
    public function getIp()
    {
        return $this->ip;
    }
}
