<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarketActivityClickList
 *
 * @ORM\Table(name="market_activity_click_list")
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\MarketActivityClickListRepository")
 */
class MarketActivityClickList
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="market_activity_id", type="integer", nullable=false)
     */
    private $marketActivityId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=false)
     */
    private $createTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set userId
     *
     * @param integer $userId
     * @return MarketActivityClickList
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
     * Set marketActivityId
     *
     * @param integer $marketActivityId
     * @return MarketActivityClickList
     */
    public function setMarketActivityId($marketActivityId)
    {
        $this->marketActivityId = $marketActivityId;

        return $this;
    }

    /**
     * Get marketActivityId
     *
     * @return integer
     */
    public function getMarketActivityId()
    {
        return $this->marketActivityId;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return MarketActivityClickList
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
