<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var varchar
     */
    private $nick;

    /**
     * @var varchar
     */
    private $pwd;

    /**
     * @var int
     */
    private $sex;

    /**
     * @var \DateTime
     */
    private $birthday;

    /**
     * @var varchar
     */
    private $email;

    /**
     * @var varchar
     */
    private $is_email_confirmed;

    /**
     * @var varchar
     */
    private $tel;

    /**
     * @var varchar
     */
    private $is_tel_confirmed;

    /**
     * @var varchar
     */
    private $city;

    /**
     * @var varchar
     */
    private $identity_num;

    /**
     * @var \DateTime
     */
    private $register_date;

    /**
     * @var \DateTime
     */
    private $last_login_date;

    /**
     * @var varchar
     */
    private $last_login_ip;

    /**
     * @var varchar
     */
    private $points;

    /**
     * @var int
     */
    private $delete_flag;


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
     * Set nick
     *
     * @param \varchar $nick
     * @return User
     */
    public function setNick(\varchar $nick)
    {
        $this->nick = $nick;
    
        return $this;
    }

    /**
     * Get nick
     *
     * @return \varchar 
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * Set pwd
     *
     * @param \varchar $pwd
     * @return User
     */
    public function setPwd(\varchar $pwd)
    {
        $this->pwd = $pwd;
    
        return $this;
    }

    /**
     * Get pwd
     *
     * @return \varchar 
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    /**
     * Set sex
     *
     * @param \int $sex
     * @return User
     */
    public function setSex(\int $sex)
    {
        $this->sex = $sex;
    
        return $this;
    }

    /**
     * Get sex
     *
     * @return \int 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set email
     *
     * @param \varchar $email
     * @return User
     */
    public function setEmail(\varchar $email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return \varchar 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set is_email_confirmed
     *
     * @param \varchar $isEmailConfirmed
     * @return User
     */
    public function setIsEmailConfirmed(\varchar $isEmailConfirmed)
    {
        $this->is_email_confirmed = $isEmailConfirmed;
    
        return $this;
    }

    /**
     * Get is_email_confirmed
     *
     * @return \varchar 
     */
    public function getIsEmailConfirmed()
    {
        return $this->is_email_confirmed;
    }

    /**
     * Set tel
     *
     * @param \varchar $tel
     * @return User
     */
    public function setTel(\varchar $tel)
    {
        $this->tel = $tel;
    
        return $this;
    }

    /**
     * Get tel
     *
     * @return \varchar 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set is_tel_confirmed
     *
     * @param \varchar $isTelConfirmed
     * @return User
     */
    public function setIsTelConfirmed(\varchar $isTelConfirmed)
    {
        $this->is_tel_confirmed = $isTelConfirmed;
    
        return $this;
    }

    /**
     * Get is_tel_confirmed
     *
     * @return \varchar 
     */
    public function getIsTelConfirmed()
    {
        return $this->is_tel_confirmed;
    }

    /**
     * Set city
     *
     * @param \varchar $city
     * @return User
     */
    public function setCity(\varchar $city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \varchar 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set identity_num
     *
     * @param \varchar $identityNum
     * @return User
     */
    public function setIdentityNum(\varchar $identityNum)
    {
        $this->identity_num = $identityNum;
    
        return $this;
    }

    /**
     * Get identity_num
     *
     * @return \varchar 
     */
    public function getIdentityNum()
    {
        return $this->identity_num;
    }

    /**
     * Set register_date
     *
     * @param \DateTime $registerDate
     * @return User
     */
    public function setRegisterDate($registerDate)
    {
        $this->register_date = $registerDate;
    
        return $this;
    }

    /**
     * Get register_date
     *
     * @return \DateTime 
     */
    public function getRegisterDate()
    {
        return $this->register_date;
    }

    /**
     * Set last_login_date
     *
     * @param \DateTime $lastLoginDate
     * @return User
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->last_login_date = $lastLoginDate;
    
        return $this;
    }

    /**
     * Get last_login_date
     *
     * @return \DateTime 
     */
    public function getLastLoginDate()
    {
        return $this->last_login_date;
    }

    /**
     * Set last_login_ip
     *
     * @param \varchar $lastLoginIp
     * @return User
     */
    public function setLastLoginIp(\varchar $lastLoginIp)
    {
        $this->last_login_ip = $lastLoginIp;
    
        return $this;
    }

    /**
     * Get last_login_ip
     *
     * @return \varchar 
     */
    public function getLastLoginIp()
    {
        return $this->last_login_ip;
    }

    /**
     * Set points
     *
     * @param \varchar $points
     * @return User
     */
    public function setPoints(\varchar $points)
    {
        $this->points = $points;
    
        return $this;
    }

    /**
     * Get points
     *
     * @return \varchar 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set delete_flag
     *
     * @param \int $deleteFlag
     * @return User
     */
    public function setDeleteFlag(\int $deleteFlag)
    {
        $this->delete_flag = $deleteFlag;
    
        return $this;
    }

    /**
     * Get delete_flag
     *
     * @return \int 
     */
    public function getDeleteFlag()
    {
        return $this->delete_flag;
    }
}
