<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserRepository")
 */
class User
{
    const POINT_SIGNUP=10;
    const POINT_EMPTY =0;

    const DEFAULT_REWARD_MULTIPE=1;

    const IS_NOT_FROM_WENWEN = 1;
    const IS_FROM_WENWEN = 2;

    const FROM_QQ_PREFIX = "QQ";
    const FROM_WEIBO_PREFIX = "WeiBo_";

   # password_choice ,== PWD_WENWEN, verify the user_wenwen_login
   # == PWD_JILI or NULL , verify by user.password
    const PWD_WENWEN = 1;
    const PWD_JILI = 2;

    const EMAIL_NOT_CONFIRMED = 0;
    const EMAIL_CONFIRMED = 1;

    public function __construct()
    {
        $this->setRegisterDate ( new \DateTime())
            ->setLastLoginDate ( new \DateTime())
            ->setPoints( self::POINT_EMPTY)
            ->setRewardMultiple( self::DEFAULT_REWARD_MULTIPE)
            ->setIsEmailConfirmed(self::EMAIL_NOT_CONFIRMED);
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=250, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pwd", type="string", length=45, nullable=true)
     */
    private $pwd;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_email_confirmed", type="integer", nullable=true)
     */
    private $isEmailConfirmed;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_from_wenwen", type="integer", nullable=true)
     */
    private $isFromWenwen;

    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=100, nullable=true)
     * @Assert\Length(
     *      min=1, 
     *      max=100,
     *      minMessage = "用户昵称为1-100个字符",
     *      maxMessage = "用户昵称为1-100个字符"
     * )
     * @Assert\NotBlank(
     *      message = "请输入您的昵称"
     * )
     */
    private $nick;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=45, nullable=true)
     * @Assert\Length(
     *      max=45,
     *      maxMessage = "请输入有效的手机号码"
     * )
     * @Assert\Type(
     *      type = "numeric",
     *      message = "请输入有效的手机号码"
     * )
     */
    private $tel;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_tel_confirmed", type="integer", nullable=true)
     */
    private $isTelConfirmed;

    /**
     * @var float
     *
     * @ORM\Column(name="reward_multiple", type="float", options={"default": 1})
     */
    private $rewardMultiple;

    /**
     * @var datetime $registerDate
     *
     * @ORM\Column(name="register_date", type="datetime", nullable=true)
     */
    private $registerDate;

    /**
     * @var datetime $registerCompleteDate
     *
     * @ORM\Column(name="register_complete_date", type="datetime", nullable=true)
     */
    private $registerCompleteDate;

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
     * @ORM\Column(name="points", type="integer")
     */
    private $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=true)
     */
    private $deleteFlag;

    /**
     * @var datetime $deleteDate
     *
     * @ORM\Column(name="delete_date", type="datetime", nullable=true)
     */
    private $deleteDate;

    /**
     * @var string
     *
     * @ORM\Column(name="icon_path", type="string",length=255, nullable=true)
     */
    private $iconPath;

    /**
     * @Assert\File(mimeTypes={"image/bmp", "image/gif", "image/jpeg", "image/png"}, maxSize="2M")
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="created_remote_addr", type="string", length=20, nullable=true, options={"comment": "remote IP when create"})
     */
    private $createdRemoteAddr;

    /**
     * @var string
     *
     * @ORM\Column(name="created_user_agent", type="text", nullable=true, options={"comment": "remote User Agent when create"})
     */
    private $createdUserAgent;

    /**
     * @var integer
     *
     * @ORM\Column(name="password_choice", type="smallint", nullable=true, options={"comment": "which password to use for login"})
     */
    private $passwordChoice;

    /**
     * @var datetime $lastGetPointsAt
     *
     * @ORM\Column(name="last_get_points_at", type="datetime", nullable=true, options={"comment": "最后一次获得(+)积分的时间"})
     */
    private $lastGetPointsAt;

    /**
     * @ORM\OneToOne(targetEntity="UserProfile", mappedBy="user", cascade="all")
     */
    private $userProfile;

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
     * Set registerCompleteDate
     *
     * @param \DateTime $registerCompleteDate
     * @return User
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
     * Set deleteDate
     *
     * @param \DateTime $deleteFlag
     * @return User
     */
    public function setDeleteDate($deleteDate)
    {
        $this->deleteDate = $deleteDate;

        return $this;
    }

    /**
     * Get deleteDate
     *
     * @return \DateTime
     */
    public function getDeleteDate()
    {
        return $this->deleteDate;
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

    public function isPwdCorrect($pwd)
    {
        return (!empty($pwd)) && $this->pw_encode($pwd) === $this->getPwd();
    }

    /**
     * Set passwordChoice
     *
     * @param integer $passwordChoice
     * @return User
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

    public function isPasswordWenwen()
    {
       $selected = $this->getPasswordChoice();
      return !is_null($selected ) && $selected  === self::PWD_WENWEN;
    }

    public function emailIsConfirmed ()
    {
        return  (bool) $this->getIsEmailConfirmed();
    }

    /**
     * Set lastGetPointsAt
     *
     * @param \DateTime $lastGetPointsAt
     * @return User
     */
    public function setLastGetPointsAt($lastGetPointsAt = null)
    {
        if(isset($lastGetPointsAt)){
            $this->lastGetPointsAt = $lastGetPointsAt;
        } else {
            $this->lastGetPointsAt = date_create();
        }
    }

    /**
     * Get lastGetPointsAt
     *
     * @return \DateTime
     */
    public function getLastGetPointsAt()
    {
        return $this->lastGetPointsAt;
    }

    /**
     * Set userProfile
     *
     * @return UserProfile
     */
    public function setUserProfile(UserProfile $userProfile)
    {
        $this->userProfile = $userProfile;
        return $this;
    }

    /**
     * Get userProfile
     *
     * @return UserProfile
     */
    public function getUserProfile()
    {
        return $this->userProfile;
    }

    public function setIcon(UploadedFile $icon)
    {
        $this->icon = $icon;
    }

    public function getIcon()
    {
        return $this->icon;
    }
}
