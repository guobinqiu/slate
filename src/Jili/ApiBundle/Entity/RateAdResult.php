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
     * @ORM\Column(name="adw_history_id", type="integer", nullable=false)
     */
    private $accessHistoryId;
    
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
     * @var integer
     *
     * @ORM\Column(name="result_price", type="integer", nullable=false)
     */
    private $resultPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="result_incentive", type="integer")
     */
    private $resultIncentive;



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
     * Set resultPrice
     *
     * @param integer $resultPrice
     * @return RateAdResult
     */
    public function setResultPrice($resultPrice)
    {
    	$this->resultPrice = $resultPrice;
    
    	return $this;
    }
    
    /**
     * Get resultPrice
     *
     * @return integer
     */
    public function getResultPrice()
    {
    	return $this->resultPrice;
    }

    /**
     * Set resultIncentive
     *
     * @param integer $resultIncentive
     * @return RateAdResult
     */
    public function setResultIncentive($resultIncentive)
    {
        $this->resultIncentive = $resultIncentive;
    
        return $this;
    }

    /**
     * Get resultIncentive
     *
     * @return integer 
     */
    public function getResultIncentive()
    {
        return $this->resultIncentive;
    }
}
