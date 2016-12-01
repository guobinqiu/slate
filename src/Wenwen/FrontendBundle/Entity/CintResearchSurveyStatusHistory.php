<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CintResearchSurveyStatusHistory
 *
 * @ORM\Table(name="survey_cint_participation_history", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="cint_research_survey_status_history_uniq", columns={"app_mid", "survey_id", "status"})
 * })
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class CintResearchSurveyStatusHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="app_mid", type="integer")
     */
    private $appMid;

    /**
     * @var integer
     *
     * @ORM\Column(name="survey_id", type="integer")
     */
    private $surveyId;

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
     * @var boolean
     *
     * @ORM\Column(name="is_answered", type="boolean")
     */
    private $isAnswered;

    /**
     * @var string
     *
     * @ORM\Column(name="client_ip", type="string", nullable=true)
     */
    private $clientIp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * @return CintResearchSurveyStatusHistory
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
     * Set surveyId
     *
     * @param integer $surveyId
     * @return CintResearchSurveyStatusHistory
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;

        return $this;
    }

    /**
     * Get surveyId
     *
     * @return integer 
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CintResearchSurveyStatusHistory
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Set isAnswered
     *
     * @param boolean $isAnswered
     * @return CintResearchSurveyStatusHistory
     */
    public function setIsAnswered($isAnswered)
    {
        $this->isAnswered = $isAnswered;

        return $this;
    }

    /**
     * Get isAnswered
     *
     * @return boolean
     */
    public function getIsAnswered()
    {
        return $this->isAnswered;
    }

    /**
     * Set clientIp
     *
     * @param string $clientIp
     * @return CintResearchSurveyStatusHistory
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
     * @return CintResearchSurveyStatusHistory
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
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }
}
