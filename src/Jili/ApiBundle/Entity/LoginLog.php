<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginLog
 */
class LoginLog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var \DateTime
     */
    private $login_date;

    /**
     * @var varchar
     */
    private $login_ip;


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
     * Set user_id
     *
     * @param \int $userId
     * @return LoginLog
     */
    public function setUserId(\int $userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return \int 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set login_date
     *
     * @param \DateTime $loginDate
     * @return LoginLog
     */
    public function setLoginDate($loginDate)
    {
        $this->login_date = $loginDate;
    
        return $this;
    }

    /**
     * Get login_date
     *
     * @return \DateTime 
     */
    public function getLoginDate()
    {
        return $this->login_date;
    }

    /**
     * Set login_ip
     *
     * @param \varchar $loginIp
     * @return LoginLog
     */
    public function setLoginIp(\varchar $loginIp)
    {
        $this->login_ip = $loginIp;
    
        return $this;
    }

    /**
     * Get login_ip
     *
     * @return \varchar 
     */
    public function getLoginIp()
    {
        return $this->login_ip;
    }
}
