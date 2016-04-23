<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RateAdResult
 *
 * @ORM\Table(name="rate_ad_result",
 *     indexes={
 *         @ORM\Index(name="fk_rate_ad_result_rate_ad1", columns={"rate_ad_id"}),
 *         @ORM\Index(name="fk_rate_ad_result_user1", columns={"user_id"})
 *     }
 * )
 * @ORM\Entity
 */
class RateAdResult
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="adw_order_id", type="integer")
     */
    private $accessHistoryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="rate_ad_id", type="integer")
     */
    private $rateAdId;

    /**
     * @var integer
     *
     * @ORM\Column(name="result_price", type="integer")
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
     * Set accessHistoryId
     *
     * @param integer $accessHistoryId
     * @return RateAdResult
     */
    public function setAccessHistoryId($accessHistoryId)
    {
        $this->accessHistoryId = $accessHistoryId;

        return $this;
    }

    /**
     * Get accessHistoryId
     *
     * @return integer
     */
    public function getAccessHistoryId()
    {
        return $this->accessHistoryId;
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
