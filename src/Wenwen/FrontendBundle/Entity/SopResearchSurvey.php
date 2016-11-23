<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * SopResearchSurvey
 *
 * @ORM\Table(name="sop_research_survey")
 * @ORM\Entity
 */
class SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
     * @return SopResearchSurvey
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
        if ($answerStatus == SurveyStatus::STATUS_COMPLETE) {
            return $this->getCompletePoint();
        } elseif ($answerStatus == SurveyStatus::STATUS_SCREENOUT) {
            return $this->getScreenoutPoint();
        } elseif ($answerStatus == SurveyStatus::STATUS_QUOTAFULL) {
            return $this->getQuotafullPoint();
        }
        return 0;
    }
}
