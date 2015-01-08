<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityGatheringCheckinLog
 *
 * @ORM\Table(name="activity_gathering_checkin_log", uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\ActivityGatheringCheckinLogRepository")
 */
class ActivityGatheringCheckinLog
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkin_at", type="datetime", nullable=false)
     */
    private $checkinAt;

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
     * Set checkinAt
     *
     * @param \DateTime $checkinAt
     * @return ActivityGatheringCheckinLog
     */
    public function setCheckinAt($checkinAt)
    {
        $this->checkinAt = $checkinAt;

        return $this;
    }

    /**
     * Get checkinAt
     *
     * @return \DateTime 
     */
    public function getCheckinAt()
    {
        return $this->checkinAt;
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
     * @return ActivityGatheringCheckinLog
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
}
