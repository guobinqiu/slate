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
     * @var datetime $accessTime
     *
     * @ORM\Column(name="access_time", type="datetime")
     */
    private $accessTime;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_type", type="integer")
     */
    private $incentiveType;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="incentive", type="integer")
     */
    private $incentive;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_rate", type="integer")
     */
    private $incentiveRate;
    
    
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
    

    
    /**
     * Set incentiveType
     *
     * @param integer $incentiveType
     * @return AdwAccessHistory
     */
    public function setIncentiveType($incentiveType)
    {
    	$this->incentiveType = $incentiveType;
    
    	return $this;
    }
    
    /**
     * Get incentiveType
     *
     * @return integer
     */
    public function getIncentiveType()
    {
    	return $this->incentiveType;
    }
    
    /**
     * Set incentive
     *
     * @param integer $incentive
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
     * @return integer
     */
    public function getIncentive()
    {
    	return $this->incentive;
    }

    
    
    /**
     * Set incentiveRate 	
     *
     * @param integer $incentiveRate
     * @return AdwAccessHistory
     */
    public function setIncentiveRate($incentiveRate)
    {
    	$this->incentiveRate = $incentiveRate;
    
    	return $this;
    }
    
    /**
     * Get incentiveRate
     *
     * @return integer
     */
    public function getIncentiveRate()
    {
    	return $this->incentiveRate;
    }

}
