<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityGatheringTaobaoOrder
 *
 * @ORM\Table(name="activity_gathering_taobao_order", uniqueConstraints={@ORM\UniqueConstraint(name="order_identity", columns={"order_identity"})})
 * @ORM\Entity
 */
class ActivityGatheringTaobaoOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Jili\FrontendBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Jili\FrontendBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_identity", referencedColumnName="id")
     * })
     */
    private $orderentity;



    /**
     * Set userId
     *
     * @param integer $userId
     * @return ActivityGatheringTaobaoOrder
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ActivityGatheringTaobaoOrder
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
     * Set orderentity
     *
     * @param \Jili\FrontendBundle\Entity\User $orderentity
     * @return ActivityGatheringTaobaoOrder
     */
    public function setOrderentity(\Jili\FrontendBundle\Entity\User $orderentity = null)
    {
        $this->orderentity = $orderentity;

        return $this;
    }

    /**
     * Get orderentity
     *
     * @return \Jili\FrontendBundle\Entity\User 
     */
    public function getOrderentity()
    {
        return $this->orderentity;
    }
}
