<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RateAd
 *
 * @ORM\Table(name="rate_ad")
 * @ORM\Entity
 */
class RateAd
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $adId;

    /**
     * @var string
     *
     * @ORM\Column(name="incentive_rate", type="string", length=45, nullable=true)
     */
    private $incentiveRate;



    /**
     * Get adId
     *
     * @return integer 
     */
    public function getAdId()
    {
        return $this->adId;
    }

    /**
     * Set incentiveRate
     *
     * @param string $incentiveRate
     * @return RateAd
     */
    public function setIncentiveRate($incentiveRate)
    {
        $this->incentiveRate = $incentiveRate;
    
        return $this;
    }

    /**
     * Get incentiveRate
     *
     * @return string 
     */
    public function getIncentiveRate()
    {
        return $this->incentiveRate;
    }
}