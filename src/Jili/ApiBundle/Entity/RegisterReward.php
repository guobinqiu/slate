<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegisterReward
 *
 * @ORM\Table(name="register_reward")
 * @ORM\Entity
 */
class RegisterReward
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

     /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

     /**
     * @var int
     *
     * @ORM\Column(name="rewards", type="integer")
     */
    private $rewards;



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
     * @return RegisterReward
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
     * Set type
     *
     * @param integer $type
     * @return RegisterReward
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
     * Set rewards
     *
     * @param integer $rewards
     * @return RegisterReward
     */
    public function setRewards($rewards)
    {
        $this->rewards = $rewards;
    
        return $this;
    }

    /**
     * Get rewards
     *
     * @return integer 
     */
    public function getRewards()
    {
        return $this->rewards;
    }

    
}
