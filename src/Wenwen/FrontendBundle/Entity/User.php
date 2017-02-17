<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Jili\ApiBundle\Utility\PasswordEncoder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})},
 *     indexes={@ORM\Index(name="user_invite_id", columns={"invite_id"})}
 * )
 * @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="邮箱地址已存在")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    const EMAIL_NOT_CONFIRMED = 0;
    const EMAIL_CONFIRMED = 1;
    const PWD_WENWEN = 1;
    const PWD_JILI = 2;
    const DEFAULT_REWARD_MULTIPE = 1;

    const POINT_EMPTY = 0;
    const POINT_SIGNUP = 0;
    const POINT_INVITE_SIGNUP = 100;

    const REMEMBER_ME_TOKEN = 'ww_passport';

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
     * @ORM\Column(name="email", type="string", length=250, nullable=true, unique=true)
     * @Assert\Email
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
     * @var \Datetime $registerDate
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $registerDate;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \Datetime $registerCompleteDate
     *
     * @ORM\Column(name="register_complete_date", type="datetime", nullable=true)
     */
    private $registerCompleteDate;

    /**
     * @var \Datetime $lastLoginDate
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
     * @var \Datetime $deleteDate
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
     * @var \Datetime $lastGetPointsAt
     *
     * @ORM\Column(name="last_get_points_at", type="datetime", nullable=true, options={"comment": "最后一次获得(+)积分的时间"})
     */
    private $lastGetPointsAt;

    /**
     * @ORM\OneToOne(targetEntity="UserProfile", mappedBy="user", cascade={"persist","remove"})
     */
    private $userProfile;

    /**
     * @ORM\OneToOne(targetEntity="UserTrack", mappedBy="user", cascade={"persist","remove"})
     */
    private $userTrack;

    /**
     * @ORM\OneToMany(targetEntity="PrizeTicket", mappedBy="user")
     */
    private $prizeTickets;

    /**
     * @ORM\OneToMany(targetEntity="UserSignInDetail", mappedBy="user")
     */
    private $userSignInDetails;

    /**
     * @ORM\OneToOne(targetEntity="UserSignInSummary", mappedBy="user")
     */
    private $userSignInSummary;

    /**
     * 注册激活token
     *
     * @ORM\Column(name="confirmation_token", type="string", nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(name="confirmation_token_expired_at", type="datetime", nullable=true)
     */
    private $confirmationTokenExpiredAt;

    /**
     * 重置密码token
     *
     * @ORM\Column(name="reset_password_token", type="string", nullable=true)
     */
    private $resetPasswordToken;

    /**
     * @ORM\Column(name="reset_password_token_expired_at", type="datetime", nullable=true)
     */
    private $resetPasswordTokenExpiredAt;

    /**
     * @var string
     *
     * @ORM\Column(name="remember_me_token", type="string", nullable=true)
     */
    private $rememberMeToken;

    /**
     * @ORM\Column(name="remember_me_token_expired_at", type="datetime", nullable=true)
     */
    private $rememberMeTokenExpiredAt;

    /**
     * 邀请人的用户id
     *
     * @ORM\Column(name="invite_id", type="integer", nullable=true)
     */
    private $inviteId;

    /**
     * @var integer
     * 参考用的，这个用户大概获得了多少cost类型的积分
     *
     * @ORM\Column(name="points_cost", type="integer", options={"default": 0})
     */
    private $pointsCost;

    /**
     * @var integer
     * 参考用的，这个用户大概获得了多少expense类型的积分
     * 
     * @ORM\Column(name="points_expense", type="integer", options={"default": 0})
     */
    private $pointsExpense;

    public function __construct()
    {
        $this->passwordChoice = self::PWD_WENWEN;
        $this->isEmailConfirmed = self::EMAIL_NOT_CONFIRMED;
        $this->points = self::POINT_EMPTY;
        $this->rewardMultiple = self::DEFAULT_REWARD_MULTIPE;
        $this->prizeTickets = new ArrayCollection();
        $this->userSignInDetails = new ArrayCollection();
        $this->pointsCost = self::POINT_EMPTY;
        $this->pointsExpense = self::POINT_EMPTY;
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
        $this->pwd = PasswordEncoder::encode('blowfish', $pwd, '★★★★★アジア事業戦略室★★★★★');

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

    /**
     * Set lastGetPointsAt
     *
     * @param \DateTime $lastGetPointsAt
     * @return User
     */
    public function setLastGetPointsAt($lastGetPointsAt)
    {
        $this->lastGetPointsAt = $lastGetPointsAt;

        return $this;
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

    /**
     * Set userTrack
     *
     * @return UserTrack
     */
    public function setUserTrack(UserTrack $userTrack)
    {
        $this->userTrack = $userTrack;

        return $this;
    }

    /**
     * Get userTrack
     *
     * @return UserTrack
     */
    public function getUserTrack()
    {
        return $this->userTrack;
    }

    public function setIcon(UploadedFile $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set confirmationTokenExpiredAt
     *
     * @param \DateTime $confirmationTokenExpiredAt
     * @return User
     */
    public function setConfirmationTokenExpiredAt($confirmationTokenExpiredAt)
    {
        $this->confirmationTokenExpiredAt = $confirmationTokenExpiredAt;

        return $this;
    }

    /**
     * Get confirmationTokenExpiredAt
     *
     * @return \DateTime
     */
    public function getConfirmationTokenExpiredAt()
    {
        return $this->confirmationTokenExpiredAt;
    }

    /**
     * Set resetPasswordToken
     *
     * @param string $resetPasswordToken
     * @return User
     */
    public function setResetPasswordToken($resetPasswordToken)
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    /**
     * Get resetPasswordToken
     *
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->resetPasswordToken;
    }

    /**
     * Set resetPasswordTokenExpiredAt
     *
     * @param \DateTime $resetPasswordTokenExpiredAt
     * @return User
     */
    public function setResetPasswordTokenExpiredAt($resetPasswordTokenExpiredAt)
    {
        $this->resetPasswordTokenExpiredAt = $resetPasswordTokenExpiredAt;

        return $this;
    }

    /**
     * Get resetPasswordTokenExpiredAt
     *
     * @return \DateTime
     */
    public function getResetPasswordTokenExpiredAt()
    {
        return $this->resetPasswordTokenExpiredAt;
    }

    /**
     * Set rememberMeToken
     *
     * @param string $rememberMeToken
     * @return User
     */
    public function setRememberMeToken($rememberMeToken)
    {
        $this->rememberMeToken = $rememberMeToken;

        return $this;
    }

    /**
     * Get rememberMeToken
     *
     * @return string
     */
    public function getRememberMeToken()
    {
        return $this->rememberMeToken;
    }

    /**
     * Set rememberMeTokenExpiredAt
     *
     * @param \DateTime $rememberMeTokenExpiredAt
     * @return User
     */
    public function setRememberMeTokenExpiredAt($rememberMeTokenExpiredAt)
    {
        $this->rememberMeTokenExpiredAt = $rememberMeTokenExpiredAt;

        return $this;
    }

    /**
     * Get $rememberMeTokenExpiredAt
     *
     * @return \DateTime
     */
    public function getRememberMeTokenExpiredAt()
    {
        return $this->rememberMeTokenExpiredAt;
    }

    public function setInviteId($inviteId)
    {
        $this->inviteId = $inviteId;

        return $this;
    }

    public function getInviteId()
    {
        return $this->inviteId;
    }

    public function getPrizeTickets()
    {
        return $this->prizeTickets;
    }

    public function getUserSignInDetails()
    {
        return $this->userSignInDetails;
    }

    public function setUserSignInSummary($userSignInSummary)
    {
        $this->userSignInSummary = $userSignInSummary;

        return $this;
    }

    public function getUserSignInSummary()
    {
        return $this->userSignInSummary;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->registerDate = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    public function isPwdCorrect($pwd)
    {
        if ($this->isPasswordWenwen()) {
            return $this->getPwd() == PasswordEncoder::encode('blowfish', $pwd, '★★★★★アジア事業戦略室★★★★★');
        }
        if ($this->isPasswordJili()) {
            return $this->getPwd() == $this->pw_encode($pwd);
        }
        return false;
    }

    public function isPasswordWenwen()
    {
        return $this->getPasswordChoice() == self::PWD_WENWEN;
    }

    public function isPasswordJili()
    {
        return $this->getPasswordChoice() == self::PWD_JILI;
    }

    public function emailIsConfirmed()
    {
        return  $this->getIsEmailConfirmed() == self::EMAIL_CONFIRMED;
    }

    /**
     * 注册token是否已过期
     */
    public function isConfirmationTokenExpired()
    {
        return new \DateTime() > $this->confirmationTokenExpiredAt;
    }

    /**
     * 重置密码token是否已过期
     */
    public function isResetPasswordTokenExpired()
    {
        return new \DateTime() > $this->resetPasswordTokenExpiredAt;
    }

    /**
     * 记住我token是否已过期
     */
    public function isRememberMeTokenExpired()
    {
        return new \DateTime() > $this->rememberMeTokenExpiredAt;
    }

    /**
     * Set pointsCost
     *
     * @param integer $pointsCost
     * @return User
     */
    public function setPointsCost($pointsCost)
    {
        $this->pointsCost = $pointsCost;

        return $this;
    }

    /**
     * Get pointsCost
     *
     * @return integer
     */
    public function getPointsCost()
    {
        return $this->pointsCost;
    }

    /**
     * Set pointsExpense
     *
     * @param integer $pointsExpense
     * @return User
     */
    public function setPointsExpense($pointsExpense)
    {
        $this->pointsExpense = $pointsExpense;

        return $this;
    }

    /**
     * Get pointsExpense
     *
     * @return integer
     */
    public function getPointsExpense()
    {
        return $this->pointsExpense;
    }
}
