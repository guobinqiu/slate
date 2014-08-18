<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarAccessHistory
 *
 * @ORM\Table(name="emar_access_history", indexes={@ORM\Index(name="fk_emar_access_record_user1", columns={"user_id"}), @ORM\Index(name="fk_emar_access_record_advertiserment1", columns={"ad_id"})})
 * @ORM\Entity
 */
class EmarAccessHistory
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
     * @ORM\Column(name="access_time", type="datetime", nullable=true)
     */
    private $accessTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Jili\EmarBundle\Entity\Advertiserment
     *
     * @ORM\ManyToOne(targetEntity="Jili\ApiBundle\Entity\Advertiserment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ad_id", referencedColumnName="id")
     * })
     */
    private $ad;



    /**
     * Set userId
     *
     * @param integer $userId
     * @return EmarAccessHistory
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
     * Set accessTime
     *
     * @param \DateTime $accessTime
     * @return EmarAccessHistory
     */
    public function setAccessTime($accessTime)
    {
        $this->accessTime = $accessTime;

        return $this;
    }

    /**
     * Get accessTime
     *
     * @return \DateTime
     */
    public function getAccessTime()
    {
        return $this->accessTime;
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
     * Set ad
     *
     * @param \Jili\ApiBundle\Entity\Advertiserment $ad
     * @return EmarAccessHistory
     */
    public function setAd(\Jili\ApiBundle\Entity\Advertiserment $ad = null)
    {
        $this->ad = $ad;

        return $this;
    }

    /**
     * Get ad
     *
     * @return \Jili\ApiBundle\Entity\Advertiserment
     */
    public function getAd()
    {
        return $this->ad;
    }
}
