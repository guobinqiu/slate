<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityGatheringTaobaoOrder
 *
 * @ORM\Table(name="activity_gathering_taobao_order", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_order_user", columns={"order_identity", "user_id"})}, indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\ActivityGatheringTaobaoOrderRepository")
 */
class ActivityGatheringTaobaoOrder
{
    /**
     * @var string
     *
     * @ORM\Column(name="order_identity", type="string", length=255, nullable=false)
     */
    private $orderIdentity;

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
     * @var \Jili\ApiBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Jili\ApiBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set orderIdentity
     *
     * @param string $orderIdentity
     * @return ActivityGatheringTaobaoOrder
     */
    public function setOrderIdentity($orderIdentity)
    {
        $this->orderIdentity = $orderIdentity;

        return $this;
    }

    /**
     * Get orderIdentity
     *
     * @return string 
     */
    public function getOrderIdentity()
    {
        return $this->orderIdentity;
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
     * Set user
     *
     * @param \Jili\ApiBundle\Entity\User $user
     * @return ActivityGatheringTaobaoOrder
     */
    public function setUser(\Jili\ApiBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Jili\ApiBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }
}
