<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @ORM\Table(name="survey_partner_participation_history",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="unique_survey_partner_id_and_user_id_and_status", columns={"survey_partner_id", "user_id", "status"})}
 * )
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\SurveyPartnerParticipationHistoryRepository")
 */
class SurveyPartnerParticipationHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="SurveyPartner", inversedBy="surveyPartnerParticipationHistorys")
     * @ORM\JoinColumn(name="survey_partner_id", referencedColumnName="id")
     */
    private $surveyPartner;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     * 这里是用来记录参与行为的唯一识别号，目前跟TripleS对接时，记录Endlink的__KEY__
     * @ORM\Column(name="u_key", type="string", length=50, nullable=true)
     */
    private $uKey;

    /**
     * @var string
     * init->(forward/reentry)->(complete/screenout/quotafull/error)
     * init: 用户在91wenwen上点击问卷列表里的回答按钮后，成为init状态，跳转至information页面
     * forward: 用户在information页面，点击回答按钮后，当该问卷不可中途退出的时候，由init变成forward状态
     * reentry: 用户在information页面，点击回答按钮后，当该问卷可以中途退出的时候，由init变成reentry状态
     * c/s/q/e: tripleS回调endlink的时候，由forward/reentry变成c/s/q/e状态
     * notallowed: 不符合条件的访问
     * @ORM\Column(name="status", type="string", length=10, nullable=false)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(name="client_ip", type="string", length=20, nullable=true)
     */
    private $clientIp;

    /**
     * @var string
     * @ORM\Column(name="cookie_key", type="string", length=36, nullable=true)
     */
    private $cookieKey;

    /**
     * @var string
     * @ORM\Column(name="browser_fingerprint", type="string", length=20, nullable=true)
     */
    private $browserFingerprint;

    /**
     * @var string
     * @ORM\Column(name="phone_number", type="string", length=20, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string
     * @ORM\Column(name="comment", type="string", length=100, nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSurveyPartner($surveyPartner)
    {
        $this->surveyPartner = $surveyPartner;
    }

    public function getSurveyPartner()
    {
        return $this->surveyPartner;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUKey($uKey)
    {
        $this->uKey = $uKey;
    }

    public function getUKey()
    {
        return $this->uKey;
    }

    public function setStatus($status)
    {
        if (!in_array($status, 
                    array(
                        SurveyStatus::STATUS_INIT,
                        SurveyStatus::STATUS_FORWARD,
                        SurveyStatus::STATUS_COMPLETE,
                        SurveyStatus::STATUS_SCREENOUT,
                        SurveyStatus::STATUS_QUOTAFULL,
                        SurveyStatus::STATUS_ERROR,
                        )
                    )
            ) 
        {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;
    }

    public function getClientIp()
    {
        return $this->clientIp;
    }

    public function setCookieKey($cookieKey)
    {
        $this->cookieKey = $cookieKey;
    }

    public function getCookieKey()
    {
        return $this->cookieKey;
    }

    public function setBrowserFingerprint($browserFingerprint)
    {
        $this->browserFingerprint = $browserFingerprint;
    }

    public function getBrowserFingerprint()
    {
        return $this->browserFingerprint;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
