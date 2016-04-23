<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OfferwowOrder
 *
 * @ORM\Table(name="offerwow_order")
 * @ORM\Entity
 */
class OfferwowOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="returned_at", type="datetime", nullable=true)
     */
    private $returnedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     */
    private $confirmedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="happened_at", type="datetime", nullable=true)
     */
    private $happenedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="eventid", type="string", length=100, nullable=true)
     */
    private $eventid;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", options={"default": 0})
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", options={"default": 0})
     */
    private $deleteFlag;

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
     * @return OfferwowOrder
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
     * @return OfferwowOrder
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
     * Set returnedAt
     *
     * @param \DateTime $returnedAt
     * @return OfferwowOrder
     */
    public function setReturnedAt($returnedAt)
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    /**
     * Get returnedAt
     *
     * @return \DateTime
     */
    public function getReturnedAt()
    {
        return $this->returnedAt;
    }

    /**
     * Set confirmedAt
     *
     * @param \DateTime $confirmedAt
     * @return OfferwowOrder
     */
    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    /**
     * Get confirmedAt
     *
     * @return \DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * Set happenedAt
     *
     * @param \DateTime $happenedAt
     * @return OfferwowOrder
     */
    public function setHappenedAt($happenedAt)
    {
        $this->happenedAt = $happenedAt;

        return $this;
    }

    /**
     * Get happenedAt
     *
     * @return \DateTime
     */
    public function getHappenedAt()
    {
        return $this->happenedAt;
    }

    /**
     * Set eventid
     *
     * @param string $eventid
     * @return OfferwowOrder
     */
    public function setEventid($eventid)
    {
        $this->eventid = $eventid;

        return $this;
    }

    /**
     * Get eventid
     *
     * @return string
     */
    public function getEventid()
    {
        return $this->eventid;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return OfferwowOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return OfferwowOrder
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;

        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return integer
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
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
