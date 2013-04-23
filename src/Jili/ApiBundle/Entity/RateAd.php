<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RateAd
 */
class RateAd
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var varchar
     */
    private $incentive_rate;


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
     * Set incentive_rate
     *
     * @param \varchar $incentiveRate
     * @return RateAd
     */
    public function setIncentiveRate(\varchar $incentiveRate)
    {
        $this->incentive_rate = $incentiveRate;
    
        return $this;
    }

    /**
     * Get incentive_rate
     *
     * @return \varchar 
     */
    public function getIncentiveRate()
    {
        return $this->incentive_rate;
    }
}
