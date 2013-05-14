<?php
namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdwAccessRecord
 *
 * @ORM\Table(name="adw_access_record")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdwAccessRecordRepository")
 */
class AdwAccessRecord
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
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userid;

   /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", nullable=true)
     */
    private $adid;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=45, nullable=true)
     */
    private $action;
    /**
     * @var datetime $time
     *
     * @ORM\Column(name="time", type="datetime", nullable=true)
     */
    private $time;

     /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=45, nullable=true)
     */
    private $key;


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
     * @return AdwAccessRecord
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
     * @return AdwAccessRecord
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
     * Set action
     *
     * @param string $action
     * @return AdwAccessRecord
     */
    public function setAction($action)
    {
        $this->action = $action;
    
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return AdwAccessRecord
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return AdwAccessRecord
     */
    public function setkey($key)
    {
        $this->key = $key;
    
        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getkey()
    {
        return $this->key;
    }
}
