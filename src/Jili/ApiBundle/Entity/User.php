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
     * @ORM\Column(name="nick", type="string", length=25, nullable=true)
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
     * @ORM\Column(name="hobby", type="integer", nullable=true)
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
     * upload image to temp dir
     */
    public function upload($upload_dir)
    {
        $fileNames = array('attachment');
        $types = array('jpg','jpeg','png');
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
	        $field = 'iconPath';
	        switch ($fileName){
	        	case 'attachment':$field = 'iconPath';break;
	        }    
	        if(!in_array($this->$fileName->guessExtension(),$types)){
	        	return 'type is jpg or png';//类型不对 
                
	        }else{
	        	if($this->$fileName->getClientSize() > 20480000){
	        		return 'size is less than 2M';//图片大于2m
	        	}else{
	        		$filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();
	        		 
	        		$this->$fileName->move($upload_dir, $filename_upload);
	        		
	        		$this->$field = $upload_dir.$filename_upload;
	        		 
	        		$this->$fileName = null;
	        		
// 	        		return '';
	        	}
	        }
        }
    }
    
    /**
     * go to email url 
     */
    function gotomail($mail){
    	$t=explode('@',$mail);
    	$t=strtolower($t[1]);
    	if($t=='163.com'){
    		return 'mail.163.com';
    	}else if($t=='vip.163.com'){
    		return 'vip.163.com';
    	}else if($t=='126.com'){
    		return 'mail.126.com';
    	}else if($t=='qq.com'||$t=='vip.qq.com'||$t=='foxmail.com'){
    		return 'mail.qq.com';
    	}else if($t=='gmail.com'){
    		return 'mail.google.com';
    	}else if($t=='sohu.com'){
    		return 'mail.sohu.com';
    	}else if($t=='tom.com'){
    		return 'mail.tom.com';
    	}else if($t=='vip.sina.com'){
    		return 'vip.sina.com';
    	}else if($t=='sina.com.cn'||$t=='sina.com'){
    		return 'mail.sina.com.cn';
    	}else if($t=='tom.com'){
    		return 'mail.tom.com';
    	}else if($t=='yahoo.com.cn'||$t=='yahoo.cn'){
    		return 'mail.cn.yahoo.com';
    	}else if($t=='tom.com'){
    		return 'mail.tom.com';
    	}else if($t=='yeah.net'){
    		return 'www.yeah.net';
    	}else if($t=='21cn.com'){
    		return 'mail.21cn.com';
    	}else if($t=='hotmail.com'){
    		return 'www.hotmail.com';
    	}else if($t=='sogou.com'){
    		return 'mail.sogou.com';
    	}else if($t=='188.com'){
    		return 'www.188.com';
    	}else if($t=='139.com'){
    		return 'mail.10086.cn';
    	}else if($t=='189.cn'){
    		return 'webmail15.189.cn/webmail';
    	}else if($t=='wo.com.cn'){
    		return 'mail.wo.com.cn/smsmail';
    	}else if($t=='139.com'){
    		return 'mail.10086.cn';
    	}else {
    		return '';
    	}
    	
    }
    
    

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
     * @return User
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
     * @param integer $isEmailConfirmed
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
     * @param integer $isTelConfirmed
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
     * @return integer 
     */
    public function getIsTelConfirmed()
    {
        return $this->isTelConfirmed;
    }

    /**
     * Set city
     *
     * @param integer $city
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
     * @return integer
     */
    public function getProfession()
    {
    	return $this->profession;
    }
    
    
    /**
     * Set hobby
     *
     * @param integer $hobby
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
     * @return integer
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
     * @param integer $points
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
     * Set isInfoSet
     *
     * @param integer $isInfoSet
     * @return User
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
    
}
