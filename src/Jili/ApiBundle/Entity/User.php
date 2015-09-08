<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserRepository")
 */
class User
{
    public $attachment;
    const POINT_SIGNUP=1;
    const POINT_EMPTY =0;

    const INFO_IS_SET=1;
    const INFO_NOT_SET=0;

    const DEFAULT_REWARD_MULTIPE=1;

    const IS_NOT_FROM_WENWEN = 1;
    const IS_FROM_WENWEN = 2;

    const FROM_QQ_PREFIX = "QQ";
    const FROM_WEIBO_PREFIX = "WeiBo_";

    public function __construct()
    {
        $this->setRegisterDate ( new \DateTime())
            ->setLastLoginDate ( new \DateTime())
            ->setPoints( self::POINT_SIGNUP)
            ->setIsInfoSet( self::INFO_IS_SET)
            ->setRewardMultiple( self::DEFAULT_REWARD_MULTIPE)
            ->setToken( '');
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
     * @ORM\Column(name="token", type="string",length=32, nullable=false)
     */
    private $token;

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
     * @var \DateTime
     * @ORM\Column(name="token_created_at", type="datetime", nullable=true)
     */
    private $tokenCreatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="origin_flag", type="boolean", nullable=true)
     */
    private $originFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="created_user_agent", type="string", length=100, nullable=true)
     */
    private $createdUserAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_code", type="string", length=100, nullable=true)
     */
    private $campaignCode;

    /**
     * upload resizeimage to temp dir
     */
    public function resizeUpload($path,$x,$y,$x1,$y1)
    {
        $size = getimagesize($path);
        // $width = $size[0];
        // $height = $size[1];
        // $max = 512;
        // $width = $max;
        // $height = $height * ($max/$size[0]);
        $src = imagecreatefromjpeg($path);
        if(!$x1)
            $x1 = 256;
         if(!$y1)
            $y1 = 256;
        $dst = imagecreatetruecolor($x1, $y1); //新建一个真彩色图像
        imagecopyresampled($dst,$src,0,0,$x,$y,$size[0],$size[1],$size[0],$size[1]);        //重采样拷贝部分图像并调整大小
        header('Content-Type: image/jpeg');
        imagejpeg($dst,$path,100);
        imagedestroy($src);
        imagedestroy($dst);

    }
    /**
     * upload image to temp dir
     */
    public function upload($upload_dir)
    {

        $fileNames = array('attachment');
        $types = array('jpg','jpeg');
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
            if($this->$fileName->getError()==1){
                return  '1';//'文件类型为jpg或png';
            }else{
                if(!in_array($this->$fileName->guessExtension(),$types)){
                    return  '1';//'文件类型为jpg或png';
                }else{
                    if($this->$fileName->getClientSize() > 2048000){
                        return  '2';//'图片大小为2M以内';
                    }else{
                        $filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();
                        //$this->$fileName->move($upload_dir, $filename_upload);
                        $size = getimagesize($this->$fileName);
                        $width = $size[0];
                        $height = $size[1];

                        $max = 512;
                        $width = $max;
                        $height = $height * ($max/$size[0]);
                        $src = imagecreatefromjpeg($this->$fileName);
                        $dst = imagecreatetruecolor($width, $height); //新建一个真彩色图像
                        imagecopyresampled($dst, $src, 0, 0, 0, 0,
                        $width, $height,$size[0], $size[1]);        //重采样拷贝部分图像并调整大小
                        header('Content-Type: image/jpeg');
                        imagejpeg($dst,$upload_dir.$filename_upload,100);
                        imagedestroy($src);
                        imagedestroy($dst);
                        $this->$field = $upload_dir.$filename_upload;
                        $this->$fileName = null;
    //                      return '';
                        return  $this->$field;

                    }
                }
            }

        }
    }

    /**
     * go to email url
     */
    public function gotomail($mail)
    {
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
        }else if($t=='me.com' || $t=='icloud.com' || $t=='mac.com'){
            return 'www.icloud.com';
        }else if($t=='263.com' || $t=='263.net' || $t=='263.net.cn' || $t=='x263.net'){
            return 'mail.263.net';
        }else if($t=='sohu.com'){
            return 'mail.sohu.com';
        }else if($t=='vip.sina.com'){
            return 'vip.sina.com';
        }else if($t=='sina.com.cn'||$t=='sina.com'||$t=='sina.cn'){
            return 'mail.sina.com.cn';
        }else if($t=='tom.com'){
            return 'mail.tom.com';
        }else if($t=='aliyun.com'){
            return 'mail.aliyun.com';
        }else if($t=='yahoo.com.cn'||$t=='yahoo.cn'){
            return 'mail.cn.yahoo.com';
        }else if($t=='hotmail.com' || $t=='outlook.com' || $t=='live.cn' ||  $t=='live.com' || $t=='msn.com'){
            return 'www.hotmail.com';
        }else if($t=='yeah.net'){
            return 'www.yeah.net';
        }else if($t=='21cn.com'){
            return 'mail.21cn.com';
        }else if($t=='sogou.com'){
            return 'mail.sogou.com';
        }else if($t=='189.cn'){
            return 'webmail15.189.cn/webmail';
        }else if($t=='wo.com.cn'){
            return 'mail.wo.com.cn/smsmail';
        }else if($t=='139.com'){
            return 'mail.10086.cn';
        }else if($t=='188.com'){
            return 'www.188.com';
        }else if($t=='xinhuanet.com'){
            return 'mail.xinhuanet.com';
        }else if($t=='eyou.com'){
            return 'www.eyou.com';
        }else if($t=='chinaren.com'){
            return 'mail.chinaren.com';
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
     * @return User
     */
    public function setWenwenUser($wenwenUser)
    {
        $this->wenwenUser = $wenwenUser;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * Set isFromWenwen
     *
     * @param integer $isFromWenwen
     * @return User
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
     * @param string $birthday
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
     * Set province
     *
     * @param integer $province
     * @return User
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
     * Set income
     *
     * @param integer $income
     * @return User
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
     * Set rewardMultiple
     *
     * @param float $rewardMultiple
     * @return User
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

     /**
     * Set uniqkey
     *
     * @param string $uniqkey
     * @return User
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

    /**
     * Set tokenCreatedAt
     *
     * @param \DateTime $tokenCreatedAt
     * @return User
     */
    public function setTokenCreatedAt($tokenCreatedAt)
    {
        $this->tokenCreatedAt = $tokenCreatedAt;

        return $this;
    }

    /**
     * Get tokenCreatedAt
     *
     * @return \DateTime
     */
    public function getTokenCreatedAt()
    {
        return $this->tokenCreatedAt;
    }

    /**
     * Set originFlag
     *
     * @param boolean $originFlag
     * @return User
     */
    public function setOriginFlag($originFlag)
    {
        $this->originFlag = $originFlag;

        return $this;
    }

    /**
     * Get originFlag
     *
     * @return boolean 
     */
    public function getOriginFlag()
    {
        return $this->originFlag;
    }

    /**
     * Set createdRemoteAddr
     *
     * @param string $createdRemoteAddr
     * @return User
     */
    public function setCreatedRemoteAddr($createdRemoteAddr)
    {
        $this->createdRemoteAddr = $createdRemoteAddr;

        return $this;
    }

    /**
     * Get createdRemoteAddr
     *
     * @return string 
     */
    public function getCreatedRemoteAddr()
    {
        return $this->createdRemoteAddr;
    }

    /**
     * Set createdUserAgent
     *
     * @param string $createdUserAgent
     * @return User
     */
    public function setCreatedUserAgent($createdUserAgent)
    {
        $this->createdUserAgent = $createdUserAgent;

        return $this;
    }

    /**
     * Get createdUserAgent
     *
     * @return string 
     */
    public function getCreatedUserAgent()
    {
        return $this->createdUserAgent;
    }

    /**
     * Set campaignCode
     *
     * @param string $campaignCode
     * @return User
     */
    public function setCampaignCode($campaignCode)
    {
        $this->campaignCode = $campaignCode;

        return $this;
    }

    /**
     * Get campaignCode
     *
     * @return string 
     */
    public function getCampaignCode()
    {
        return $this->campaignCode;
    }
}
