<?php

namespace Jili\ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CanserUser
 *
 * @ORM\Table(name="canser_user")
 * @ORM\Entity
 */
class CanserUser
{
	public function __construct() {
		$this->registerDate = new \DateTime();
		$this->lastLoginDate = new \DateTime();
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
     * @ORM\Column(name="is_from_wenwen",  type="integer", nullable=true)
     */
    private $isFromWenwen;
    
    /**
     * @var string
     *
     * @ORM\Column(name="wenwen_user", type="string", length=100, nullable=true)
     */
    private $wenwenUser;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=100, nullable=true)
     */
    private $nick;

    /**
     * @var string
     *
     * @ORM\Column(name="pwd", type="string", length=45, nullable=true)
     */
    private $pwd;

    /**
     * @var integer
     *
     * @ORM\Column(name="sex", type="integer", nullable=true)
     */
    private $sex;

    /**
     * @var date
     *
     * @ORM\Column(name="birthday", type="string",length=50, nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=250, nullable=true)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_email_confirmed", type="integer",nullable=true)
     */
    private $isEmailConfirmed;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=45, nullable=true)
     */
    private $tel;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_tel_confirmed", type="integer" , nullable=true)
     */
    private $isTelConfirmed;

    /**
     * @var integer
     *
     * @ORM\Column(name="province", type="integer", nullable=true)
     */
    private $province;

    /**
     * @var integer
     *
     * @ORM\Column(name="city", type="integer", nullable=true)
     */
    private $city;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="education", type="integer", nullable=true)
     */
    private $education;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="profession", type="integer", nullable=true)
     */
    private $profession;

    /**
     * @var integer
     *
     * @ORM\Column(name="income", type="integer", nullable=true)
     */
    private $income;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="hobby", type="string", length=250, nullable=true)
     */
    private $hobby;
   
    /**
     * @var text
     *
     * @ORM\Column(name="personalDes", type="text", nullable=true)
     */
    private $personalDes;

    /**
     * @var string
     *
     * @ORM\Column(name="identity_num", type="string", length=40, nullable=true)
     */
    private $identityNum;

    /**
     * @var float
     *
     * @ORM\Column(name="reward_multiple", type="float")
     */
    private $rewardMultiple;

    /**
     * @var datetime $registerDate
     *
     * @ORM\Column(name="register_date", type="datetime", nullable=true)
     */
    private $registerDate;

    /**
     *@var datetime $lastLoginDate
     *
     * @ORM\Column(name="last_login_date", type="datetime", nullable=true)
     */
    private $lastLoginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="last_login_ip", type="string", length=20, nullable=true)
     */
    private $lastLoginIp;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=true)
     */
    private $deleteFlag;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_info_set", type="integer")
     */
    private $isInfoSet;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="icon_path", type="string",length=255, nullable=true)
     */
    private $iconPath;
    
     /**
     * @var string
     * 
     * @ORM\Column(name="uniqkey", type="string",length=250, nullable=true)
     */
    private $uniqkey;
        
    /**
     * Get iconPath
     *
     * @return string
     */
    public function getIconPath()
    {
    	return $this->iconPath;
    }
    
    
     /**
     * Set iconPath
     *
     * @param string $iconPath
     * @return CanserUser
     */
    public function setIconPath($iconPath)
    {
    	$this->iconPath = $iconPath;
    }
   
    
    
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
     * @return CanserUser
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
     * Get wenwenUser
     *
     * @return string
     */
    public function getWenwenUser()
    {
    	return $this->wenwenUser;
    }
    
    
    /**
     * Set wenwenUser
     *
     * @param string $wenwenUser
     * @return CanserUser
     */
    public function setWenwenUser($wenwenUser)
    {
    	$this->wenwenUser = $wenwenUser;
    }
    
    

    /**
     * Set isFromWenwen
     *
     * @param integer $isFromWenwen
     * @return CanserUser
     */
    public function setIsFromWenwen($isFromWenwen)
    {
    	$this->isFromWenwen = $isFromWenwen;
    
    	return $this;
    }
    
    
    
    /**
     * Get isFromWenwen
     *
     * @return integer
     */
    public function getIsFromWenwen()
    {
    	return $this->isFromWenwen;
    }
    
    
    
    /**
     * Set nick
     *
     * @param string $nick
     * @return CanserUser
     */
    public function setNick($nick)
    {
        $this->nick = $nick;
    
        return $this;
    }

    /**
     * Get nick
     *
     * @return string 
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * Set pwd
     *
     * @param string $pwd
     * @return CanserUser
     */
    public function setPwd($pwd)
    {
        $this->pwd = $this->pw_encode($pwd);
    
        return $this;
    }

    /**
     * Get pwd
     *
     * @return string 
     */
    public function getPwd()
    {
        return $this->pwd;
    }
    
    /**
     * sha1 pwd
     *
     * @return string
     */
    public function pw_encode($pwd)
    {
    	$seed = '';
    	for ($i = 1; $i <= 9; $i++)
    		$seed .= sha1($pwd.'0123456789abcdef');
    		for ($i = 1; $i <= 11; $i++)
    		$seed .= sha1($seed);
    		return sha1($seed);
    }
    
    
   

    /**
     * Set sex
     *
     * @param integer $sex
     * @return CanserUser
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    
        return $this;
    }
    
    

    /**
     * Get sex
     *
     * @return integer 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set birthday
     *
     * @param string $birthday
     * @return CanserUser
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return string 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return CanserUser
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isEmailConfirmed
     *
     * @param integer $isEmailConfirmed
     * @return CanserUser
     */
    public function setIsEmailConfirmed($isEmailConfirmed)
    {
        $this->isEmailConfirmed = $isEmailConfirmed;
    
        return $this;
    }

    /**
     * Get isEmailConfirmed
     *
     * @return integer 
     */
    public function getIsEmailConfirmed()
    {
        return $this->isEmailConfirmed;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return CanserUser
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    
        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set isTelConfirmed
     *
     * @param integer $isTelConfirmed
     * @return CanserUser
     */
    public function setIsTelConfirmed($isTelConfirmed)
    {
        $this->isTelConfirmed = $isTelConfirmed;
    
        return $this;
    }

    /**
     * Get isTelConfirmed
     *
     * @return integer 
     */
    public function getIsTelConfirmed()
    {
        return $this->isTelConfirmed;
    }

     /**
     * Set province
     *
     * @param integer $province
     * @return CanserUser
     */
    public function setProvince($province)
    {
        $this->province = $province;
    
        return $this;
    }
    
    /**
     * Get province
     *
     * @return integer
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set city
     *
     * @param integer $city
     * @return CanserUser
     */
    public function setCity($city)
    {
    	$this->city = $city;
    
    	return $this;
    }
    
    /**
     * Get city
     *
     * @return integer
     */
    public function getCity()
    {
    	return $this->city;
    }
    
    
    /**
     * Set education
     *
     * @param integer $education
     * @return CanserUser
     */
    public function setEducation($education)
    {
    	$this->education = $education;
    
    	return $this;
    }
    
    /**
     * Get education
     *
     * @return integer
     */
    public function getEducation()
    {
    	return $this->education;
    }
    
    
    /**
     * Set profession
     *
     * @param integer $profession
     * @return CanserUser
     */
    public function setProfession($profession)
    {
    	$this->profession = $profession;
    
    	return $this;
    }
    
    /**
     * Get profession
     *
     * @return integer
     */
    public function getProfession()
    {
    	return $this->profession;
    }


     /**
     * Set income
     *
     * @param integer $income
     * @return CanserUser
     */
    public function setIncome($income)
    {
        $this->income = $income;
    
        return $this;
    }
    
    
    
    /**
     * Get income
     *
     * @return integer
     */
    public function getIncome()
    {
        return $this->income;
    }
    
    
    
    /**
     * Set hobby
     *
     * @param string $hobby
     * @return CanserUser
     */
    public function setHobby($hobby)
    {
    	$this->hobby = $hobby;
    
    	return $this;
    }
    
    /**
     * Get hobby
     *
     * @return string
     */
    public function getHobby()
    {
    	return $this->hobby;
    }
    
    
    /**
     * Set personalDes
     *
     * @param string $personalDes
     * @return CanserUser
     */
    public function setPersonalDes($personalDes)
    {
    	$this->personalDes = $personalDes;
    
    	return $this;
    }
    
    /**
     * Get personalDes
     *
     * @return string
     */
    public function getPersonalDes()
    {
    	return $this->personalDes;
    }
    
    

    /**
     * Set identityNum
     *
     * @param string $identityNum
     * @return CanserUser
     */
    public function setIdentityNum($identityNum)
    {
        $this->identityNum = $identityNum;
    
        return $this;
    }

    /**
     * Get identityNum
     *
     * @return string 
     */
    public function getIdentityNum()
    {
        return $this->identityNum;
    }

 /**
     * Set rewardMultiple
     *
     * @param float $rewardMultiple
     * @return CanserUser
     */
    public function setRewardMultiple($rewardMultiple)
    {
        $this->rewardMultiple = $rewardMultiple;
    
        return $this;
    }

    /**
     * Get rewardMultiple
     *
     * @return float 
     */
    public function getRewardMultiple()
    {
        return $this->rewardMultiple;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     * @return CanserUser
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    
        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime 
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Set lastLoginDate
     *
     * @param \DateTime $lastLoginDate
     * @return CanserUser
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;
    
        return $this;
    }

    /**
     * Get lastLoginDate
     *
     * @return \DateTime 
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * Set lastLoginIp
     *
     * @param string $lastLoginIp
     * @return CanserUser
     */
    public function setLastLoginIp($lastLoginIp)
    {
        $this->lastLoginIp = $lastLoginIp;
    
        return $this;
    }

    /**
     * Get lastLoginIp
     *
     * @return string 
     */
    public function getLastLoginIp()
    {
        return $this->lastLoginIp;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return CanserUser
     */
    public function setPoints($points)
    {
        $this->points = $points;
    
        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return CanserUser
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
    
    
    /**
     * Set isInfoSet
     *
     * @param integer $isInfoSet
     * @return CanserUser
     */
    public function setIsInfoSet($isInfoSet)
    {
    	$this->isInfoSet = $isInfoSet;
    
    	return $this;
    }
    
    /**
     * Get isInfoSet
     *
     * @return integer
     */
    public function getIsInfoSet()
    {
    	return $this->isInfoSet;
    }

     /**
     * Set uniqkey
     *
     * @param string $uniqkey
     * @return CanserUser
     */
    public function setUniqkey($uniqkey)
    {
        $this->uniqkey = $uniqkey;
    
        return $this;
    }

    /**
     * Get uniqkey
     *
     * @return string 
     */
    public function getUniqkey()
    {
        return $this->uniqkey;
    }
    
}
