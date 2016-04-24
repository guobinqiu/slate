<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LimitAdResult
 *
 * @ORM\Table(name="limit_ad_result")
 * @ORM\Entity
 */
class LimitAdResult
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
     * @ORM\Column(name="limit_ad_id", type="integer")
     */
    private $limitAdId;

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
     * @return LimitAdResult
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
     * Set limitAdId
     *
     * @param integer $limitAdId
     * @return LimitAdResult
     */
    public function setLimitAdId($limitAdId)
    {
        $this->limitAdId = $limitAdId;

        return $this;
    }

    /**
     * Get limitAdId
     *
     * @return integer
     */
    public function getLimitAdId()
    {
        return $this->limitAdId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return LimitAdResult
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
     * Set resultIncentive
     *
     * @param integer $resultIncentive
     * @return LimitAdResult
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
