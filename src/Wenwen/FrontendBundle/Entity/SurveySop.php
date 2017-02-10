<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * SurveySop
 *
 * @ORM\Table(name="survey_sop")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class SurveySop
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
     * @ORM\Column(name="survey_id", type="integer", unique=true)
     */
    private $surveyId;

    /**
     * @var integer
     *
     * @ORM\Column(name="quota_id", type="integer")
     */
    private $quotaId;

    /**
     * @var integer
     *
     * @ORM\Column(name="loi", type="integer", nullable=true)
     */
    private $loi;

    /**
     * @var integer
     *
     * @ORM\Column(name="ir", type="integer", nullable=true)
     */
    private $ir;

    /**
     * @var float
     *
     * @ORM\Column(name="cpi", type="float", nullable=true)
     */
    private $cpi;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="complete_point", type="integer", nullable=true)
     */
    private $completePoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="screenout_point", type="integer", nullable=true)
     */
    private $screenoutPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="quotafull_point", type="integer", nullable=true)
     */
    private $quotafullPoint;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pc_blocked", type="boolean", nullable=true)
     */
    private $pcBlocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mobile_blocked", type="boolean", nullable=true)
     */
    private $mobileBlocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tablet_blocked", type="boolean", nullable=true)
     */
    private $tabletBlocked;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_closed", type="boolean", nullable=true)
     */
    private $isClosed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_fixed_loi", type="boolean", nullable=true)
     */
    private $isFixedLoi;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_notifiable", type="boolean", nullable=true)
     */
    private $isNotifiable;

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
     * @var string
     *
     * @ORM\Column(name="point_type", type="string", nullable=true)
     */
    private $pointType;

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
     * Set surveyId
     *
     * @param integer $surveyId
     * @return SurveySop
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
     * Set quotaId
     *
     * @param integer $quotaId
     * @return SurveySop
     */
    public function setQuotaId($quotaId)
    {
        $this->quotaId = $quotaId;

        return $this;
    }

    /**
     * Get quotaId
     *
     * @return integer 
     */
    public function getQuotaId()
    {
        return $this->quotaId;
    }

    /**
     * Set loi
     *
     * @param integer $loi
     * @return SurveySop
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
     * Set ir
     *
     * @param integer $ir
     * @return SurveySop
     */
    public function setIr($ir)
    {
        $this->ir = $ir;

        return $this;
    }

    /**
     * Get ir
     *
     * @return integer 
     */
    public function getIr()
    {
        return $this->ir;
    }

    /**
     * Set cpi
     *
     * @param float $cpi
     * @return SurveySop
     */
    public function setCpi($cpi)
    {
        $this->cpi = $cpi;

        return $this;
    }

    /**
     * Get cpi
     *
     * @return float 
     */
    public function getCpi()
    {
        return $this->cpi;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SurveySop
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
     * Set completePoint
     *
     * @param integer $completePoint
     * @return SurveySop
     */
    public function setCompletePoint($completePoint)
    {
        $this->completePoint = $completePoint;

        return $this;
    }

    /**
     * Get completePoint
     *
     * @return integer 
     */
    public function getCompletePoint()
    {
        return $this->completePoint;
    }

    /**
     * Set screenoutPoint
     *
     * @param integer $screenoutPoint
     * @return SurveySop
     */
    public function setScreenoutPoint($screenoutPoint)
    {
        $this->screenoutPoint = $screenoutPoint;

        return $this;
    }

    /**
     * Get screenoutPoint
     *
     * @return integer 
     */
    public function getScreenoutPoint()
    {
        return $this->screenoutPoint;
    }

    /**
     * Set quotafullPoint
     *
     * @param integer $quotafullPoint
     * @return SurveySop
     */
    public function setQuotafullPoint($quotafullPoint)
    {
        $this->quotafullPoint = $quotafullPoint;

        return $this;
    }

    /**
     * Get quotafullPoint
     *
     * @return integer 
     */
    public function getQuotafullPoint()
    {
        return $this->quotafullPoint;
    }

    /**
     * Set pcBlocked
     *
     * @param boolean $pcBlocked
     * @return SurveySop
     */
    public function setPcBlocked($pcBlocked)
    {
        $this->pcBlocked = $pcBlocked;

        return $this;
    }

    /**
     * Get pcBlocked
     *
     * @return boolean 
     */
    public function isPcBlocked()
    {
        return $this->pcBlocked;
    }

    /**
     * Set mobileBlocked
     *
     * @param boolean $mobileBlocked
     * @return SurveySop
     */
    public function setMobileBlocked($mobileBlocked)
    {
        $this->mobileBlocked = $mobileBlocked;

        return $this;
    }

    /**
     * Get mobileBlocked
     *
     * @return boolean 
     */
    public function isMobileBlocked()
    {
        return $this->mobileBlocked;
    }

    /**
     * Set tabletBlocked
     *
     * @param boolean $tabletBlocked
     * @return SurveySop
     */
    public function setTabletBlocked($tabletBlocked)
    {
        $this->tabletBlocked = $tabletBlocked;

        return $this;
    }

    /**
     * Get tabletBlocked
     *
     * @return boolean 
     */
    public function isTabletBlocked()
    {
        return $this->tabletBlocked;
    }

    /**
     * Get pcBlocked
     *
     * @return boolean 
     */
    public function getPcBlocked()
    {
        return $this->pcBlocked;
    }

    /**
     * Get mobileBlocked
     *
     * @return boolean 
     */
    public function getMobileBlocked()
    {
        return $this->mobileBlocked;
    }

    /**
     * Get tabletBlocked
     *
     * @return boolean 
     */
    public function getTabletBlocked()
    {
        return $this->tabletBlocked;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return SurveySop
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return SurveySop
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return SurveySop
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param $answerStatus
     * @return int
     */
    public function getPoints($answerStatus) {
        $points = null;
        if ($answerStatus == SurveyStatus::STATUS_COMPLETE) {
            $points = $this->getCompletePoint();
        } elseif ($answerStatus == SurveyStatus::STATUS_SCREENOUT) {
            $points = $this->getScreenoutPoint();
        } elseif ($answerStatus == SurveyStatus::STATUS_QUOTAFULL) {
            $points = $this->getQuotafullPoint();
        }
        return $points == null ? 0 : $points;
    }

    /**
     * Set isClosed
     *
     * @param boolean $isClosed
     * @return SurveySop
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    /**
     * Get isClosed
     *
     * @return boolean 
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * Set isFixedLoi
     *
     * @param boolean $isFixedLoi
     * @return SurveySop
     */
    public function setIsFixedLoi($isFixedLoi)
    {
        $this->isFixedLoi = $isFixedLoi;

        return $this;
    }

    /**
     * Get isFixedLoi
     *
     * @return boolean 
     */
    public function getIsFixedLoi()
    {
        return $this->isFixedLoi;
    }

    /**
     * Set isNotifiable
     *
     * @param boolean $isNotifiable
     * @return SurveySop
     */
    public function setIsNotifiable($isNotifiable)
    {
        $this->isNotifiable = $isNotifiable;

        return $this;
    }

    /**
     * Get isNotifiable
     *
     * @return boolean 
     */
    public function getIsNotifiable()
    {
        return $this->isNotifiable;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SurveySop
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
     * @return SurveySop
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
     * @return SurveySop
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
     * Set pointType
     *
     * @param string $pointType
     * @return SurveySop
     */
    public function setPointType($pointType)
    {
        $this->pointType = $pointType;

        return $this;
    }

    /**
     * Get pointType
     *
     * @return string
     */
    public function getPointType()
    {
        return $this->pointType;
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
