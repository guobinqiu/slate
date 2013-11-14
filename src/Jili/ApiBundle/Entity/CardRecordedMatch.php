<?php

namespace Jili\ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CardRecordedMatch
 *
 * @ORM\Table(name="card_recorded_match")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\CardRecordedMatchRepository")
 */
class CardRecordedMatch
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
     * @ORM\Column(name="match_count",  type="integer")
     */
    private $matchCount;
    
    
 	/**
     * @var datetime $createTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_provide_flag", type="integer",nullable=true)
     */
    private $isProvideFlag;


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
     * @return CardRecordedMatch
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
     * Set matchCount
     *
     * @param integer $matchCount
     * @return CardRecordedMatch
     */
    public function setMatchCount($matchCount)
    {
    	$this->matchCount = $matchCount;
    
    	return $this;
    }
    
    /**
     * Get matchCount
     *
     * @return integer
     */
    public function getMatchCount()
    {
    	return $this->matchCount;
    }

     /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return CardRecordedMatch
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
     * Set isProvideFlag
     *
     * @param integer $isProvideFlag
     * @return CardRecordedMatch
     */
    public function setIsProvideFlag($isProvideFlag)
    {
        $this->isProvideFlag = $isProvideFlag;
    
        return $this;
    }
    
    /**
     * Get isProvideFlag
     *
     * @return integer
     */
    public function getIsProvideFlag()
    {
        return $this->isProvideFlag;
    }
    

}
