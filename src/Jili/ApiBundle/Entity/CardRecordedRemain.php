<?php

namespace Jili\ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CardRecordedRemain
 *
 * @ORM\Table(name="card_recorded_remain")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\CardRecordedRemainRepository")
 */
class CardRecordedRemain
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
     * @ORM\Column(name="user_id",  type="integer", nullable=false)
     */
    private $userId;
        
    /**
     * @var integer
     *
     * @ORM\Column(name="remain_count",  type="integer")
     */
    private $remainCount;
    
    
 	/**
     * @var datetime $createTime
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
     * Set userId
     *
     * @param integer $userId
     * @return CardRecordedRemain
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
     * Set remainCount
     *
     * @param integer $remainCount
     * @return CardRecordedRemain
     */
    public function setRemainCount($remainCount)
    {
    	$this->remainCount = $remainCount;
    
    	return $this;
    }
    
    
    
    /**
     * Get remainCount
     *
     * @return integer
     */
    public function getRemainCount()
    {
    	return $this->remainCount;
    }

     /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return CardRecordedRemain
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
