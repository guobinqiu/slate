<?php
namespace Jili\ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdwOrder
 *
 * @ORM\Table(name="adw_order")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdwOrderRepository")
 */
class AdwOrder
{
	public function __construct() {
		$this->createTime = new \DateTime();
		$this->adwReturnTime = new \DateTime();
		$this->confirmTime = new \DateTime();
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
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;
    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="adw_return_time", type="datetime")
     */
    private $adwReturnTime;
    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirm_time", type="datetime")
     */
    private $confirmTime;

    
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
     * @var integer
     *
     * @ORM\Column(name="order_status", type="integer")
     */
    private $orderStatus;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer")
     */
    private $deleteFlag;
    
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
     * @return AdwOrder
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
     * @return AdwOrder
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
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return AdwOrder
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
    
    
    
    /**
     * Set adwReturnTime
     *
     * @param \DateTime $adwReturnTime
     * @return AdwOrder
     */
    public function setAdwReturnTime($adwReturnTime)
    {
    	$this->adwReturnTime = $adwReturnTime;
    
    	return $this;
    }
    
    /**
     * Get adwReturnTime
     *
     * @return \DateTime
     */
    public function getAdwReturnTime()
    {
    	return $this->adwReturnTime;
    }
    
    
    
    /**
     * Set confirmTime
     *
     * @param \DateTime $confirmTime
     * @return AdwOrder
     */
    public function setConfirmTime($confirmTime)
    {
    	$this->confirmTime = $confirmTime;
    
    	return $this;
    }
    
    /**
     * Get confirmTime
     *
     * @return \DateTime
     */
    public function getConfirmTime()
    {
    	return $this->confirmTime;
    }
    

    
    /**
     * Set incentiveType
     *
     * @param integer $incentiveType
     * @return AdwOrder
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
     * @return AdwOrder
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
     * @return AdwOrder
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
    
    
    

    /**
     * Set orderStatus
     *
     * @param integer $orderStatus
     * @return AdwOrder
     */
    public function setOrderStatus($orderStatus)
    {
    	$this->orderStatus = $orderStatus;
    
    	return $this;
    }
    
    /**
     * Get orderStatus
     *
     * @return integer
     */
    public function getOrderStatus()
    {
    	return $this->orderStatus;
    }
    
    
    

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return AdwOrder
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
    

}
