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
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", nullable=false)
     */
    private $adId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="income_rate", type="integer", nullable=false)
     */
    private $incomeRate;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_rate", type="integer", nullable=false)
     */
    private $incentiveRate;



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
     * Set adId
     *
     * @param integer $adId
     * @return RateAd
     */
    public function setAdId($adId)
    {
    	$this->adId = $adId;
    
    	return $this;
    }
    
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
     * Set incomeRate
     *
     * @param integer $incomeRate
     * @return RateAd
     */
    public function setIncomeRate($incomeRate)
    {
    	$this->incomeRate = $incomeRate;
    
    	return $this;
    }
    
    /**
     * Get incomeRate
     *
     * @return integer
     */
    public function getIncomeRate()
    {
    	return $this->incomeRate;
    }
    

    /**
     * Set incentiveRate
     *
     * @param integer $incentiveRate
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
     * @return integer 
     */
    public function getIncentiveRate()
    {
        return $this->incentiveRate;
    }
}