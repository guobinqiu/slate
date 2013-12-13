<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckinAdverList
 *
 * @ORM\Table(name="checkin_adver_list")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\CheckinAdverListRepository")
 */
class CheckinAdverList
{
    public function __construct() {
        $this->createTime = new \DateTime();
    }

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
     * @ORM\Column(name="ad_id", type="integer")
     */
    private $adId;

    /**
     * @var integer
     *
     * @ORM\Column(name="inter_space", type="integer")
     */
    private $interSpace;

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
     * Set adId
     *
     * @param integer $adId
     * @return CheckinAdverList
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
     * Set interSpace
     *
     * @param integer $interSpace
     * @return CheckinAdverList
     */
    public function setInterSpace($interSpace)
    {
        $this->interSpace = $interSpace;
    
        return $this;
    }

    /**
     * Get interSpace
     *
     * @return integer 
     */
    public function getInterSpace()
    {
        return $this->interSpace;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return CheckinAdverList
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
