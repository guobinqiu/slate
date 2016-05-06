<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ActivityGatheringTaobaoOrder
 *
 * @ORM\Table(name="activity_gathering_taobao_order",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="user_order", columns={"user_id", "order_identity"})
 *     },
 *     indexes={
 *         @ORM\Index(name="IDX_93358419A76ED395", columns={"user_id"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\ActivityGatheringTaobaoOrderRepository")
 *
 * @UniqueEntity(
 *     fields={"user", "orderIdentity"},
 *     errorPath="orderIdentity",
 *     message="你已经提交过相同的订单号."
 * )
 *
 */
class ActivityGatheringTaobaoOrder
{
    /**
     * @var string
     *
     * @ORM\Column(name="order_identity", type="string", length=255)
     *
     * @Assert\Regex(
     *     pattern="/^\d{15,16}$/",
     *     message="需要填0~9组成的订单号"
     * )
     * @Assert\Length(
     *      min = 15,
     *      max = 16,
     *      exactMessage ="需要填15-16位订单号"
     * )
     *
     */
    private $orderIdentity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     *
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
     * @ORM\ManyToOne(targetEntity="Jili\ApiBundle\Entity\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
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
