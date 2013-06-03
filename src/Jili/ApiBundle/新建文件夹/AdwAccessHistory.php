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
	public function __construct() {
		$this->adtime = new \DateTime();
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
     * @var datetime $adtime
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
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="incentive", type="integer", nullable=true)
     */
    private $incentive;
    
    
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
     * Set action
     *
     * @param string $action
     * @return AdwAccessHistory
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
     * @return AdwAccessHistory
     */
    public function setAdTime($adtime)
    {
        $this->adtime = $adtime;
    
        return $this;
    }

    /**
     * Get adtime
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
     * @return AdwAccessHistory
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
     * @return AdwAccessHistory
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
    
    
    /**
     * Set type
     *
     * @param integer $type
     * @return AdwAccessHistory
     */
    public function setType($type)
    {
    	$this->type = $type;
    
    	return $this;
    }
    
    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
    	return $this->type;
    }
    
    /**
     * Set incentive
     *
     * @param string $incentive
     * @return AdwAccessHistory
     */
    public function setIncentive($incentive)
    {
    	$this->incentive = $incentive;
    
    	return $this;
    }
    
    /**
     * Get incentive
     *
     * @return string
     */
    public function getIncentive()
    {
    	return $this->incentive;
    }


}
