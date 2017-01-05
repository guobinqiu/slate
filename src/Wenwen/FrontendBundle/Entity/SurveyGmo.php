<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyGmo
 *
 * @ORM\Table(name="survey_gmo")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class SurveyGmo
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
     * Survey Arrival Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="arrival_day", type="datetime")
     */
    private $arrivalDay;

    /**
     * Encrypted ID = Panelist ID:Panel Code:Random String
     * Redirect to questionnaire
     *
     * @var string
     *
     * @ORM\Column(name="encrypt_id", type="string")
     */
    private $encryptId;

    /**
     * Panel ID (for Questionnaire link )
     *
     * @var string
     *
     * @ORM\Column(name="panel_id", type="string")
     */
    private $panelId;

    /**
     * Questionnaire link of URL redirection
     *
     * @var string
     *
     * @ORM\Column(name="redirect_st", type="string")
     */
    private $redirectSt;

    /**
     * Survey ID
     *
     * @var integer
     *
     * @ORM\Column(name="research_id", type="integer")
     */
    private $researchId;

    /**
     * @var integer
     *
     * @ORM\Column(name="research_type", type="integer", nullable=true)
     */
    private $researchType;

    /**
     * Not-Answered , Answered, Expired
     *
     * @var string
     *
     * @ORM\Column(name="situation", type="string")
     */
    private $situation;

    /**
     * Survey Name
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * 01:Not-Answered 02:Answered
     *
     * @var string
     *
     * @ORM\Column(name="ans_stat_cd", type="string", nullable=true)
     */
    private $ansStatCd;

    /**
     * Quota Group Status
     * 00:Under Construction 05:Middle of Fieldwork
     * 07:Survey Complete 99:Deleted
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;

    /**
     * Quota Group per Panel Status
     * 05:Middle of Fieldwork 07:Survey Complete
     *
     * @var string
     *
     * @ORM\Column(name="enq_per_panel_status", type="string", nullable=true)
     */
    private $enqPerPanelStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer")
     */
    private $point;

    /**
     * Minimum Point
     *
     * @var integer
     *
     * @ORM\Column(name="point_min", type="integer")
     */
    private $pointMin;

    /**
     * @var string
     *
     * @ORM\Column(name="point_string", type="string", nullable=true)
     */
    private $pointString;

    /**
     * @var
     *
     * @ORM\Column(name="point_type", type="integer", nullable=true)
     */
    private $pointType;

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
     * @var \DateTime
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var integer
     *
     *
     */
    private $startDt;
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
     * Set arrivalDay
     *
     * @param \DateTime $arrivalDay
     * @return SurveyGmo
     */
    public function setArrivalDay($arrivalDay)
    {
        $this->arrivalDay = $arrivalDay;

        return $this;
    }

    /**
     * Get arrivalDay
     *
     * @return \DateTime 
     */
    public function getArrivalDay()
    {
        return $this->arrivalDay;
    }

    /**
     * Set encryptId
     *
     * @param string $encryptId
     * @return SurveyGmo
     */
    public function setEncryptId($encryptId)
    {
        $this->encryptId = $encryptId;

        return $this;
    }

    /**
     * Get encryptId
     *
     * @return string 
     */
    public function getEncryptId()
    {
        return $this->encryptId;
    }

    /**
     * Set panelId
     *
     * @param string $panelId
     * @return SurveyGmo
     */
    public function setPanelId($panelId)
    {
        $this->panelId = $panelId;

        return $this;
    }

    /**
     * Get panelId
     *
     * @return string 
     */
    public function getPanelId()
    {
        return $this->panelId;
    }

    /**
     * Set redirectSt
     *
     * @param string $redirectSt
     * @return SurveyGmo
     */
    public function setRedirectSt($redirectSt)
    {
        $this->redirectSt = $redirectSt;

        return $this;
    }

    /**
     * Get redirectSt
     *
     * @return string 
     */
    public function getRedirectSt()
    {
        return $this->redirectSt;
    }

    /**
     * Set researchId
     *
     * @param integer $researchId
     * @return SurveyGmo
     */
    public function setResearchId($researchId)
    {
        $this->researchId = $researchId;

        return $this;
    }

    /**
     * Get researchId
     *
     * @return integer 
     */
    public function getResearchId()
    {
        return $this->researchId;
    }

    /**
     * Set researchType
     *
     * @param integer $researchType
     * @return SurveyGmo
     */
    public function setResearchType($researchType)
    {
        $this->researchType = $researchType;

        return $this;
    }

    /**
     * Get researchType
     *
     * @return integer 
     */
    public function getResearchType()
    {
        return $this->researchType;
    }

    /**
     * Set situation
     *
     * @param string $situation
     * @return SurveyGmo
     */
    public function setSituation($situation)
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * Get situation
     *
     * @return string 
     */
    public function getSituation()
    {
        return $this->situation;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SurveyGmo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set ansStatCd
     *
     * @param string $ansStatCd
     * @return SurveyGmo
     */
    public function setAnsStatCd($ansStatCd)
    {
        $this->ansStatCd = $ansStatCd;

        return $this;
    }

    /**
     * Get ansStatCd
     *
     * @return string 
     */
    public function getAnsStatCd()
    {
        return $this->ansStatCd;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return SurveyGmo
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
     * Set enqPerPanelStatus
     *
     * @param string $enqPerPanelStatus
     * @return SurveyGmo
     */
    public function setEnqPerPanelStatus($enqPerPanelStatus)
    {
        $this->enqPerPanelStatus = $enqPerPanelStatus;

        return $this;
    }

    /**
     * Get enqPerPanelStatus
     *
     * @return string 
     */
    public function getEnqPerPanelStatus()
    {
        return $this->enqPerPanelStatus;
    }

    /**
     * Set point
     *
     * @param integer $point
     * @return SurveyGmo
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer 
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set pointMin
     *
     * @param integer $pointMin
     * @return SurveyGmo
     */
    public function setPointMin($pointMin)
    {
        $this->pointMin = $pointMin;

        return $this;
    }

    /**
     * Get pointMin
     *
     * @return integer 
     */
    public function getPointMin()
    {
        return $this->pointMin;
    }

    /**
     * Set pointString
     *
     * @param string $pointString
     * @return SurveyGmo
     */
    public function setPointString($pointString)
    {
        $this->pointString = $pointString;

        return $this;
    }

    /**
     * Get pointString
     *
     * @return string 
     */
    public function getPointString()
    {
        return $this->pointString;
    }

    /**
     * Set pointType
     *
     * @param integer $pointType
     * @return SurveyGmo
     */
    public function setPointType($pointType)
    {
        $this->pointType = $pointType;

        return $this;
    }

    /**
     * Get pointType
     *
     * @return integer 
     */
    public function getPointType()
    {
        return $this->pointType;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SurveyGmo
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return SurveyGmo
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
     * Set closedAt
     *
     * @param \DateTime $closedAt
     * @return SurveyGmo
     */
    public function setClosedAt($closedAt)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Get closedAt
     *
     * @return \DateTime 
     */
    public function getClosedAt()
    {
        return $this->closedAt;
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
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
