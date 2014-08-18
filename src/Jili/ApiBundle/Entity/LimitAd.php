<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LimitAd
 *
 * @ORM\Table(name="limit_ad")
 * @ORM\Entity
 */
class LimitAd
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
     * @ORM\Column(name="ad_id", type="integer")
     */
    private $adId;

    /**
     * @var integer
     *
     * @ORM\Column(name="income", type="integer")
     */
    private $income;


    /**
     * @var integer
     *
     * @ORM\Column(name="incentive", type="integer")
     */
    private $incentive;



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
     * @return LimitAd
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
     * Set income
     *
     * @param integer $income
     * @return LimitAd
     */
    public function setIncome($income)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income
     *
     * @return integer
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set incentive
     *
     * @param integer $incentive
     * @return LimitAd
     */
    public function setIncentive($incentive)
    {
        $this->incentive = $incentive;

        return $this;
    }

    /**
     * Get incentive
     *
     * @return integer
     */
    public function getIncentive()
    {
        return $this->incentive;
    }



}
