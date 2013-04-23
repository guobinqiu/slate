<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RateAdResult
 */
class RateAdResult
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
     * @var int
     */
    private $rate_ad_id;

    /**
     * @var varchar
     */
    private $result_point;


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
     * @return RateAdResult
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
     * Set rate_ad_id
     *
     * @param \int $rateAdId
     * @return RateAdResult
     */
    public function setRateAdId(\int $rateAdId)
    {
        $this->rate_ad_id = $rateAdId;
    
        return $this;
    }

    /**
     * Get rate_ad_id
     *
     * @return \int 
     */
    public function getRateAdId()
    {
        return $this->rate_ad_id;
    }

    /**
     * Set result_point
     *
     * @param \varchar $resultPoint
     * @return RateAdResult
     */
    public function setResultPoint(\varchar $resultPoint)
    {
        $this->result_point = $resultPoint;
    
        return $this;
    }

    /**
     * Get result_point
     *
     * @return \varchar 
     */
    public function getResultPoint()
    {
        return $this->result_point;
    }
}
