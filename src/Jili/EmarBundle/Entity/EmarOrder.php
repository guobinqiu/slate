<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarOrder
 *
 * @ORM\Table(name="emar_order",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="adid_ocd_uniq", columns={"ad_id", "ocd"})},
 *            indexes={@ORM\Index(name="ad_id_ref", columns={"ad_id", "ad_type"})}
 *           )
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarOrderRepository")
 */
class EmarOrder
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
     * @ORM\Column(name="ad_id", type="integer", nullable=false)
     */
    private $adId;

    /**
     * @var string
     *
     * @ORM\Column(name="ad_type", type="string", length=16, nullable=false, options={"default":"emar", "comment":"表示ad_id对应, local: advertiserment, emar: open.yiqifa.ad.get"})
     */
    private $adType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
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
     * @var float
     *
     * @ORM\Column(name="comm", type="float", precision=10, scale=0, nullable=true)
     */
    private $comm;

    /**
     * @var string
     *
     * @ORM\Column(name="ocd", type="string", length=100, nullable=true)
     */
    private $ocd;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default":0})
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=false, options={"default":0})
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
     * @return EmarOrder
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
     * Set adId
     *
     * @param integer $adId
     * @return EmarOrder
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
     * Set adType
     *
     * @param string $adType
     * @return EmarOrder
     */
    public function setAdType($adType)
    {
        $this->adType = $adType;

        return $this;
    }

    /**
     * Get adType
     *
     * @return string
     */
    public function getAdType()
    {
        return $this->adType;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return EmarOrder
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
     * @return EmarOrder
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
     * @return EmarOrder
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
     * @return EmarOrder
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
     * Set comm
     *
     * @param float $comm
     * @return EmarOrder
     */
    public function setComm($comm)
    {
        $this->comm = $comm;

        return $this;
    }

    /**
     * Get comm
     *
     * @return float
     */
    public function getComm()
    {
        return $this->comm;
    }

    /**
     * Set ocd
     *
     * @param string $ocd
     * @return EmarOrder
     */
    public function setOcd($ocd)
    {
        $this->ocd = $ocd;

        return $this;
    }

    /**
     * Get ocd
     *
     * @return string
     */
    public function getOcd()
    {
        return $this->ocd;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return EmarOrder
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
     * @return EmarOrder
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
