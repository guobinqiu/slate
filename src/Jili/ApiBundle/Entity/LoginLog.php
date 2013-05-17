<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginLog
 *
 * @ORM\Table(name="login_log")
 * @ORM\Entity
 */
class LoginLog
{
	
	public function __construct() {
		$this->loginDate = new \DateTime();
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
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var datetime $loginDate
     *
     * @ORM\Column(name="login_date", type="datetime", nullable=true)
     */
    private $loginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="login_ip", type="string", length=45, nullable=true)
     */
    private $loginIp;



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
     * @return LoginLog
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
     * Set loginDate
     *
     * @param \DateTime $loginDate
     * @return LoginLog
     */
    public function setLoginDate($loginDate)
    {
        $this->loginDate = $loginDate;
    
        return $this;
    }

    /**
     * Get loginDate
     *
     * @return \DateTime 
     */
    public function getLoginDate()
    {
        return $this->loginDate;
    }

    /**
     * Set loginIp
     *
     * @param string $loginIp
     * @return LoginLog
     */
    public function setLoginIp($loginIp)
    {
        $this->loginIp = $loginIp;
    
        return $this;
    }

    /**
     * Get loginIp
     *
     * @return string 
     */
    public function getLoginIp()
    {
        return $this->loginIp;
    }
}