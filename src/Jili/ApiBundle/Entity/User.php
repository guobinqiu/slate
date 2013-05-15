<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User
{
	public $attachment;
	
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
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=45, nullable=true)
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
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="is_email_confirmed", type="string", length=45, nullable=true)
     */
    private $isEmailConfirmed;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=45, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="is_tel_confirmed", type="string", length=45, nullable=true)
     */
    private $isTelConfirmed;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=45, nullable=true)
     */
    private $city;
    
    /**
     * @var string
     *
     * @ORM\Column(name="education", type="string", length=45, nullable=true)
     */
    private $education;
    
    /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=45, nullable=true)
     */
    private $profession;
    
    /**
     * @var string
     *
     * @ORM\Column(name="hobby", type="string", length=45, nullable=true)
     */
    private $hobby;
   
    /**
     * @var string
     *
     * @ORM\Column(name="personalDes", type="string", length=45, nullable=true)
     */
    private $personalDes;

    /**
     * @var string
     *
     * @ORM\Column(name="identity_num", type="string", length=40, nullable=true)
     */
    private $identityNum;

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
     * @ORM\Column(name="last_login_ip", type="string", length=45, nullable=true)
     */
    private $lastLoginIp;

    /**
     * @var string
     *
     * @ORM\Column(name="points", type="string", length=45, nullable=true)
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
     * @ORM\Column(name="flag", type="integer", nullable=false)
     */
    private $flag;

    
    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string",length=255, nullable=true)
     */
    private $path;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string",length=45, nullable=true)
     */
    private $code;

    
/**
     * upload image to temp dir
     */
    public function upload($upload_dir)
    {
        $fileNames = array('attachment');
        
        $upload_dir .= $this->getId()%100;
        if(!is_dir($upload_dir)){
        	mkdir($upload_dir,0777);
        }
        $upload_dir.='/';
        foreach ($fileNames as $key=>$fileName){
        	$filename_upload = '';
        	if (null === $this->$fileName) {
	            unset($fileNames[$key]);
	            continue ;
	        }
	        $field = 'path';
	        switch ($fileName){
	        	case 'attachment':$field = 'path';break;
	        }    
	    
	        $filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();
	        
	        $this->$fileName->move($upload_dir, $filename_upload);
	
	        $this->$field = $upload_dir.$filename_upload;
			
	        $this->$fileName = null;
        }
    }
    

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
    	return $this->path;
    }
    
    
     /**
     * Set path
     *
     * @param string $path
     * @return User
     */
    public function setPath($path)
    {
    	$this->path = $path;
    }
   
    
    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
    	return $this->code;
    }
    
    
    /**
     * Set path
     *
     * @param string $code
     * @return User
     */
    public function setCode($code)
    {
    	$this->code = $code;
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
     * Set nick
     *
     * @param string $nick
     * @return User
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
     * @return User
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
     * @return User
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
     * @param string $email
     * @return User
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
     * @param string $isEmailConfirmed
     * @return User
     */
    public function setIsEmailConfirmed($isEmailConfirmed)
    {
        $this->isEmailConfirmed = $isEmailConfirmed;
    
        return $this;
    }

    /**
     * Get isEmailConfirmed
     *
     * @return string 
     */
    public function getIsEmailConfirmed()
    {
        return $this->isEmailConfirmed;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return User
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
     * @param string $isTelConfirmed
     * @return User
     */
    public function setIsTelConfirmed($isTelConfirmed)
    {
        $this->isTelConfirmed = $isTelConfirmed;
    
        return $this;
    }

    /**
     * Get isTelConfirmed
     *
     * @return string 
     */
    public function getIsTelConfirmed()
    {
        return $this->isTelConfirmed;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
    	$this->city = $city;
    
    	return $this;
    }
    
    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
    	return $this->city;
    }
    
    
    /**
     * Set education
     *
     * @param string $education
     * @return User
     */
    public function setEducation($education)
    {
    	$this->education = $education;
    
    	return $this;
    }
    
    /**
     * Get education
     *
     * @return string
     */
    public function getEducation()
    {
    	return $this->education;
    }
    
    
    /**
     * Set profession
     *
     * @param string $profession
     * @return User
     */
    public function setProfession($profession)
    {
    	$this->profession = $profession;
    
    	return $this;
    }
    
    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
    	return $this->profession;
    }
    
    
    /**
     * Set hobby
     *
     * @param string $hobby
     * @return User
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
     * @return User
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
     * @return User
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
     * Set registerDate
     *
     * @param \DateTime $registerDate
     * @return User
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
     * @return User
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
     * @return User
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
     * @param string $points
     * @return User
     */
    public function setPoints($points)
    {
        $this->points = $points;
    
        return $this;
    }

    /**
     * Get points
     *
     * @return string 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return User
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
     * Set flag
     *
     * @param integer $flag
     * @return User
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
    
    
    
}