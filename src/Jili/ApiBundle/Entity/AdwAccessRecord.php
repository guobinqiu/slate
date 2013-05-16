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
	public function __construct() {
		$this->time = new \DateTime();
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
     * @ORM\Column(name="ad_time", type="datetime", nullable=true)
     */
    private $adtime;

     /**
     * @var string
     *
     * @ORM\Column(name="ad_key", type="string", length=45, nullable=true)
     */
    private $adkey;

    
    /**
     * @var int
     *
     * @ORM\Column(name="flag", type="integer", nullable=true)
     */
    private $flag;

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
     * Set adtime
     *
     * @param \DateTime $adtime
     * @return AdwAccessRecord
     */
    public function setAdTime($adtime)
    {
        $this->adtime = $adtime;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getAdTime()
    {
        return $this->adtime;
    }
    

    /**
     * Set adkey
     *
     * @param string $adkey
     * @return AdwAccessRecord
     */
    public function setAdKey($adkey)
    {
        $this->adkey = $adkey;
    
        return $this;
    }

    /**
     * Get adkey
     *
     * @return string 
     */
    public function getAdKey()
    {
        return $this->adkey;
    }
    
    
    /**
     * Set flag
     *
     * @param integer $flag
     * @return AdwAccessRecord
     */
    public function setFlag($flag)
    {
    	$this->flag = $flag;
    
    	return $this;
    }
    
    /**
     * Get flag
     *
     * @return integer
     */
    public function getFlag()
    {
    	return $this->flag;
    }
}
