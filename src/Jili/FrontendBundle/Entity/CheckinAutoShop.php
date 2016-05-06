<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

     
/**
 * CheckinClickList
 *
 * @ORM\Table(name="checkin_auto_shop", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_user_id1_checkin_adver_list_id1", columns={"user_id","checkin_adver_list_id"})} )
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\CheckinAutoShopRepository")
 */
class CheckinAutoShop
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;


    /**
     * @var integer
     *
     * @ORM\Column(name="checkin_adver_list_id", type="integer",nullable=false)
     */
    private $checkinAdverListId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createAt;

    public function __construct()
    {
        $this->createTime = new \DateTime();
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

    /**
     * Set userId
     *
     * @param integer $userId
     * @return CheckinAutoShop
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
     * Set checkinAdverListId
     *
     * @param integer $checkinAdverListId
     * @return CheckinAutoShop
     */
    public function setCheckinAdverListId($checkinAdverListId)
    {
        $this->checkinAdverListId = $checkinAdverListId;

        return $this;
    }

    /**
     * Get checkinAdverListId
     *
     * @return integer 
     */
    public function getCheckinAdverListId()
    {
        return $this->checkinAdverListId;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return CheckinAutoShop
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }
}
