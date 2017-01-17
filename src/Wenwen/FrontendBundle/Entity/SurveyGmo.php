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
     * Survey ID
     *
     * @var integer
     *
     * @ORM\Column(name="research_id", type="integer")
     */
    private $researchId;

    /**
     * Survey Name
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * Quota Group Status (00:Under Construction 05:Middle of Fieldwork 07:Survey Complete 99:Deleted)
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=2)
     */
    private $status;

    /**
     * point = screener point + main survey point
     *
     * @var integer
     *
     * @ORM\Column(name="point", type="integer")
     */
    private $point;

    /**
     * 被甄别了就是screener point，否则和point相同
     *
     * Minimum Point
     *
     * @var integer
     *
     * @ORM\Column(name="point_min", type="integer")
     */
    private $pointMin;

    /**
     * @var
     *
     * @ORM\Column(name="enq_per_panel_status", length=2)
     */
    private $enqPerPanelStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="arrival_day", type="string")
     */
    private $arrivalDay;

    /**
     * @var integer
     *
     * @ORM\Column(name="loi", type="integer", nullable=true)
     */
    private $loi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

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

    /**
     * @return boolean
     */
    public function isClosed()
    {
        if ('05' == $this->getStatus() && '05' == $this->getEnqPerPanelStatus()) {
            return false;
        }
        return true;
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
     * Set arrivalDay
     *
     * @param string $arrivalDay
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
     * @return string
     */
    public function getArrivalDay()
    {
        return $this->arrivalDay;
    }

    /**
     * Set loi
     *
     * @param integer $loi
     * @return SurveyGmo
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
}
