<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyGmoParticipationHistory
 *
 * @ORM\Table(name="survey_gmo_participation_history", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="survey_gmo_participation_history_uniq", columns={"user_id", "survey_gmo_id", "status"})
 * })
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class SurveyGmoParticipationHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

//    /**
//     * @var integer
//     *
//     * @ORM\Column(name="app_mid", type="integer")
//     */
//    private $appMid;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="survey_gmo_id", type="integer")
     */
    private $surveyGmoId;

    /**
     *                                 |--> complete
     * targeted --> init --> forward --|--> screenout
     *                                 |--> quotafull
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="client_ip", type="string", nullable=true)
     */
    private $clientIp;

    /**
     * @var integer
     *
     * @ORM\Column(name="loi", type="integer", nullable=true)
     */
    private $loi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

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
     * Set appMid
     *
     * @param integer $appMid
     * @return SurveyGmoParticipationHistory
     */
    public function setAppMid($appMid)
    {
        $this->appMid = $appMid;

        return $this;
    }

    /**
     * Get appMid
     *
     * @return integer
     */
    public function getAppMid()
    {
        return $this->appMid;
    }

    /**
     * Set surveyGmoId
     *
     * @param integer $surveyGmoId
     * @return SurveyGmoParticipationHistory
     */
    public function setSurveyGmoId($surveyGmoId)
    {
        $this->surveyGmoId = $surveyGmoId;

        return $this;
    }

    /**
     * Get surveyGmoId
     *
     * @return integer 
     */
    public function getSurveyGmoId()
    {
        return $this->surveyGmoId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return SurveyGmoParticipationHistory
     */
    public function setStatus($status)
    {
        $this->status = strtolower($status);

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set clientIp
     *
     * @param string $clientIp
     * @return SurveyGmoParticipationHistory
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    /**
     * Get clientIp
     *
     * @return string 
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SurveyGmoParticipationHistory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set loi
     *
     * @param integer $loi
     * @return SurveySopParticipationHistory
     */
    public function setLoi($loi)
    {
        $this->loi = $loi;

        return $this;
    }

    /**
     * Get loi
     *
     * @return integer
     */
    public function getLoi()
    {
        return $this->loi;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return SurveySopParticipationHistory
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
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return SurveyGmoParticipationHistory
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
}