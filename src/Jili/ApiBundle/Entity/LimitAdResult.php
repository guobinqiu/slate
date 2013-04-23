<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LimitAdResult
 */
class LimitAdResult
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var int
     */
    private $limit_ad_id;

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
     * Set limit_ad_id
     *
     * @param \int $limitAdId
     * @return LimitAdResult
     */
    public function setLimitAdId(\int $limitAdId)
    {
        $this->limit_ad_id = $limitAdId;
    
        return $this;
    }

    /**
     * Get limit_ad_id
     *
     * @return \int 
     */
    public function getLimitAdId()
    {
        return $this->limit_ad_id;
    }

    /**
     * Set result_point
     *
     * @param \varchar $resultPoint
     * @return LimitAdResult
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
