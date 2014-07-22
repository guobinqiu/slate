<?php
namespace Jili\ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdwAccessHistory
 *
 * @ORM\Table(name="adw_access_history")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdwAccessHistoryRepository")
 */
class AdwAccessHistory
{
    public function __construct()
    {
        $this->accessTime = new \DateTime();
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

   /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer")
     */
    private $adid;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="access_time", type="datetime")
     */
    private $accessTime;


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
     * Set userid
     *
     * @param integer $userid
     * @return AdwAccessHistory
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set adid
     *
     * @param integer $adid
     * @return AdwAccessHistory
     */
    public function setAdid($adid)
    {
        $this->adid = $adid;

        return $this;
    }

    /**
     * Get adid
     *
     * @return integer
     */
    public function getAdid()
    {
        return $this->adid;
    }


    /**
     * Set accessTime
     *
     * @param \DateTime $accessTime
     * @return AdwAccessHistory
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


}
