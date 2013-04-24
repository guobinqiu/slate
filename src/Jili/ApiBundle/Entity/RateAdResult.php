<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RateAdResult
 *
 * @ORM\Table(name="rate_ad_result")
 * @ORM\Entity
 */
class RateAdResult
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
     * @var integer
     *
     * @ORM\Column(name="rate_ad_id", type="integer", nullable=false)
     */
    private $rateAdId;

    /**
     * @var string
     *
     * @ORM\Column(name="result_point", type="string", length=45, nullable=true)
     */
    private $resultPoint;



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
     * @return RateAdResult
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
     * Set rateAdId
     *
     * @param integer $rateAdId
     * @return RateAdResult
     */
    public function setRateAdId($rateAdId)
    {
        $this->rateAdId = $rateAdId;
    
        return $this;
    }

    /**
     * Get rateAdId
     *
     * @return integer 
     */
    public function getRateAdId()
    {
        return $this->rateAdId;
    }

    /**
     * Set resultPoint
     *
     * @param string $resultPoint
     * @return RateAdResult
     */
    public function setResultPoint($resultPoint)
    {
        $this->resultPoint = $resultPoint;
    
        return $this;
    }

    /**
     * Get resultPoint
     *
     * @return string 
     */
    public function getResultPoint()
    {
        return $this->resultPoint;
    }
}