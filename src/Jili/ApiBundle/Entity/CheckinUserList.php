<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckinUserList
 *
 * @ORM\Table(name="checkin_user_list")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\CheckinUserListRepository")
 */
class CheckinUserList
{
    public function __construct()
    {
        $this->createTime = new \DateTime();
    }

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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="click_date", type="string" ,length=20)
     */
    private $clickDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="open_shop_id", type="integer", options={"comment": "对应 checkin_adver_list的id"})
     */
    private $openShopId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;


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
     * @return CheckinUserList
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
     * Set clickDate
     *
     * @param string $clickDate
     * @return CheckinUserList
     */
    public function setClickDate($clickDate)
    {
        $this->clickDate = $clickDate;

        return $this;
    }

    /**
     * Get clickDate
     *
     * @return string
     */
    public function getClickDate()
    {
        return $this->clickDate;
    }


    /**
     * Set openShopId
     *
     * @param integer $openShopId
     * @return CheckinUserList
     */
    public function setOpenShopId($openShopId)
    {
        $this->openShopId = $openShopId;

        return $this;
    }

    /**
     * Get openShopId
     *
     * @return integer
     */
    public function getOpenShopId()
    {
        return $this->openShopId;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return CheckinUserList
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



}
