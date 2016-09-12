<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_deleted")
 * @ORM\Entity
 */
class UserDeleted
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
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
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=100, nullable=true)
     */
    private $nick;

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
     * @var float
     *
     * @ORM\Column(name="reward_multiple", type="float", options={"default": 1})
     */
    private $rewardMultiple;

    /**
     * @var datetime $registerDate
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $registerDate;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var datetime $registerCompleteDate
     *
     * @ORM\Column(name="register_complete_date", type="datetime", nullable=true)
     */
    private $registerCompleteDate;

    /**
     * @var datetime $lastLoginDate
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return UserDeleted
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
     * Set deleteDate
     *
     * @param \DateTime $deleteDate
     * @return UserDeleted
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
     * Set lastGetPointsAt
     *
     * @param \DateTime $lastGetPointsAt
     * @return UserDeleted
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
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
     * @return UserDeleted
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
}
