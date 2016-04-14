<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserDeleted
 *
 * @ORM\Table(name="user_deleted", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity
 */
class UserDeleted
{
    /**
     * @var integer
     *
     * @ORM\Column(name="is_from_wenwen", type="integer", nullable=true)
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
     * @ORM\Column(name="token", type="string", length=32, nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="birthday", type="string", length=50, nullable=true)
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
     * @ORM\Column(name="is_email_confirmed", type="integer", nullable=true)
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
     * @ORM\Column(name="is_tel_confirmed", type="integer", nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="hobby", type="string", length=250, nullable=true)
     */
    private $hobby;

    /**
     * @var string
     *
     * @ORM\Column(name="personalDes", type="text", nullable=true)
     */
    private $personaldes;

    /**
     * @var string
     *
     * @ORM\Column(name="identity_num", type="string", length=40, nullable=true)
     */
    private $identityNum;

    /**
     * @var float
     *
     * @ORM\Column(name="reward_multiple", type="float", precision=10, scale=0, nullable=false)
     */
    private $rewardMultiple;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_date", type="datetime", nullable=true)
     */
    private $registerDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_complete_date", type="datetime", nullable=true)
     */
    private $registerCompleteDate;

    /**
     * @var \DateTime
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
     * @ORM\Column(name="is_info_set", type="integer", nullable=false)
     */
    private $isInfoSet;

    /**
     * @var string
     *
     * @ORM\Column(name="icon_path", type="string", length=255, nullable=true)
     */
    private $iconPath;

    /**
     * @var string
     *
     * @ORM\Column(name="uniqkey", type="string", length=250, nullable=true)
     */
    private $uniqkey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="token_created_at", type="datetime", nullable=true)
     */
    private $tokenCreatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="origin_flag", type="smallint", nullable=true)
     */
    private $originFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="created_remote_addr", type="string", length=20, nullable=true)
     */
    private $createdRemoteAddr;

    /**
     * @var string
     *
     * @ORM\Column(name="created_user_agent", type="text", nullable=true)
     */
    private $createdUserAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_code", type="string", length=100, nullable=true)
     */
    private $campaignCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="password_choice", type="smallint", nullable=true)
     */
    private $passwordChoice;

    /**
     * @var string
     *
     * @ORM\Column(name="fav_music", type="string", length=255, nullable=true)
     */
    private $favMusic;

    /**
     * @var string
     *
     * @ORM\Column(name="monthly_wish", type="string", length=255, nullable=true)
     */
    private $monthlyWish;

    /**
     * @var integer
     *
     * @ORM\Column(name="industry_code", type="integer", nullable=true)
     */
    private $industryCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="work_section_code", type="integer", nullable=true)
     */
    private $workSectionCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set isFromWenwen
     *
     * @param integer $isFromWenwen
     * @return UserDeleted
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
     * Set wenwenUser
     *
     * @param string $wenwenUser
     * @return UserDeleted
     */
    public function setWenwenUser($wenwenUser)
    {
        $this->wenwenUser = $wenwenUser;

        return $this;
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
     * Set token
     *
     * @param string $token
     * @return UserDeleted
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
     * Set nick
     *
     * @param string $nick
     * @return UserDeleted
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
     * @return UserDeleted
     */
    public function setPwd($pwd)
    {
        $this->pwd = $pwd;

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
     * Set sex
     *
     * @param integer $sex
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * Set personaldes
     *
     * @param string $personaldes
     * @return UserDeleted
     */
    public function setPersonaldes($personaldes)
    {
        $this->personaldes = $personaldes;

        return $this;
    }

    /**
     * Get personaldes
     *
     * @return string 
     */
    public function getPersonaldes()
    {
        return $this->personaldes;
    }

    /**
     * Set identityNum
     *
     * @param string $identityNum
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * Set registerCompleteDate
     *
     * @param \DateTime $registerCompleteDate
     * @return UserDeleted
     */
    public function setRegisterCompleteDate($registerCompleteDate)
    {
        $this->registerCompleteDate = $registerCompleteDate;

        return $this;
    }

    /**
     * Get registerCompleteDate
     *
     * @return \DateTime 
     */
    public function getRegisterCompleteDate()
    {
        return $this->registerCompleteDate;
    }

    /**
     * Set lastLoginDate
     *
     * @param \DateTime $lastLoginDate
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * Set iconPath
     *
     * @param string $iconPath
     * @return UserDeleted
     */
    public function setIconPath($iconPath)
    {
        $this->iconPath = $iconPath;

        return $this;
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
     * Set uniqkey
     *
     * @param string $uniqkey
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @param integer $originFlag
     * @return UserDeleted
     */
    public function setOriginFlag($originFlag)
    {
        $this->originFlag = $originFlag;

        return $this;
    }

    /**
     * Get originFlag
     *
     * @return integer 
     */
    public function getOriginFlag()
    {
        return $this->originFlag;
    }

    /**
     * Set createdRemoteAddr
     *
     * @param string $createdRemoteAddr
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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

    /**
     * Set passwordChoice
     *
     * @param integer $passwordChoice
     * @return UserDeleted
     */
    public function setPasswordChoice($passwordChoice)
    {
        $this->passwordChoice = $passwordChoice;

        return $this;
    }

    /**
     * Get passwordChoice
     *
     * @return integer 
     */
    public function getPasswordChoice()
    {
        return $this->passwordChoice;
    }

    /**
     * Set favMusic
     *
     * @param string $favMusic
     * @return UserDeleted
     */
    public function setFavMusic($favMusic)
    {
        $this->favMusic = $favMusic;

        return $this;
    }

    /**
     * Get favMusic
     *
     * @return string 
     */
    public function getFavMusic()
    {
        return $this->favMusic;
    }

    /**
     * Set monthlyWish
     *
     * @param string $monthlyWish
     * @return UserDeleted
     */
    public function setMonthlyWish($monthlyWish)
    {
        $this->monthlyWish = $monthlyWish;

        return $this;
    }

    /**
     * Get monthlyWish
     *
     * @return string 
     */
    public function getMonthlyWish()
    {
        return $this->monthlyWish;
    }

    /**
     * Set industryCode
     *
     * @param integer $industryCode
     * @return UserDeleted
     */
    public function setIndustryCode($industryCode)
    {
        $this->industryCode = $industryCode;

        return $this;
    }

    /**
     * Get industryCode
     *
     * @return integer 
     */
    public function getIndustryCode()
    {
        return $this->industryCode;
    }

    /**
     * Set workSectionCode
     *
     * @param integer $workSectionCode
     * @return UserDeleted
     */
    public function setWorkSectionCode($workSectionCode)
    {
        $this->workSectionCode = $workSectionCode;

        return $this;
    }

    /**
     * Get workSectionCode
     *
     * @return integer 
     */
    public function getWorkSectionCode()
    {
        return $this->workSectionCode;
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
}
