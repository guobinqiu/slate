<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExchangeFromWenwen
 *
 * @ORM\Table(name="exchange_from_wenwen")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\ExchangeFromWenwenRepository")
 */
class ExchangeFromWenwen
{
	public function __construct() {
		$this->createTime = new \DateTime();
	}
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="wenwen_exchange_id", type="string", length=50)
     */
    private $wenwenExchangeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=250)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="payment_point", type="integer")
     */
    private $paymentPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

   
    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=50)
     */
    private $reason;
    

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;


    
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
     * Set reason
     *
     * @param string $reason
     * @return ExchangeFromWenwen
     */
    public function setReason($reason)
    {
    	$this->reason = $reason;
    
    	return $this;
    }
    
    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
    	return $this->reason;
    }
    

    /**
     * Set wenwenExchangeId
     *
     * @param string $wenwenExchangeId
     * @return ExchangeFromWenwen
     */
    public function setWenwenExchangeId($wenwenExchangeId)
    {
        $this->wenwenExchangeId = $wenwenExchangeId;
    
        return $this;
    }

    /**
     * Get wenwenExchangeId
     *
     * @return string 
     */
    public function getWenwenExchangeId()
    {
        return $this->wenwenExchangeId;
    }


    /**
     * Set userId
     *
     * @param integer $userId
     * @return ExchangeFromWenwen
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
     * Set status
     *
     * @param integer $status
     * @return ExchangeFromWenwen
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
     * Set paymentPoint
     *
     * @param integer $paymentPoint
     * @return ExchangeFromWenwen
     */
    public function setPaymentPoint($paymentPoint)
    {
        $this->paymentPoint = $paymentPoint;
    
        return $this;
    }

    /**
     * Get paymentPoint
     *
     * @return integer 
     */
    public function getPaymentPoint()
    {
        return $this->paymentPoint;
    }

     /**
     * Set  email
     *
     * @param string $email
     * @return ExchangeFromWenwen
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    
    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return ExchangeFromWenwen
     */
    public function setCreateTime($createTime)
    {
    	$this->createTime = $createTime;
    
    	return $this;
    }
    
    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
    	return $this->createTime;
    }
    
}
