<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CintResearchSurveyParticipationHistory
 *
 * @ORM\Table(name="cint_research_survey_participation_history", uniqueConstraints={@ORM\UniqueConstraint(name="cint_project_member_uniq", columns={"cint_project_id", "app_member_id"})})
 * @ORM\Entity
 */
class CintResearchSurveyParticipationHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cint_project_id", type="integer", nullable=false)
     */
    private $cintProjectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="cint_project_quota_id", type="integer", nullable=false)
     */
    private $cintProjectQuotaId;

    /**
     * @var string
     *
     * @ORM\Column(name="app_member_id", type="string", length=255, nullable=false)
     */
    private $appMemberId;

    /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer", nullable=false)
     */
    private $point;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="stash_data", type="text", nullable=true)
     */
    private $stashData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set cintProjectId
     *
     * @param integer $cintProjectId
     * @return CintResearchSurveyParticipationHistory
     */
    public function setCintProjectId($cintProjectId)
    {
        $this->cintProjectId = $cintProjectId;

        return $this;
    }

    /**
     * Get cintProjectId
     *
     * @return integer 
     */
    public function getCintProjectId()
    {
        return $this->cintProjectId;
    }

    /**
     * Set cintProjectQuotaId
     *
     * @param integer $cintProjectQuotaId
     * @return CintResearchSurveyParticipationHistory
     */
    public function setCintProjectQuotaId($cintProjectQuotaId)
    {
        $this->cintProjectQuotaId = $cintProjectQuotaId;

        return $this;
    }

    /**
     * Get cintProjectQuotaId
     *
     * @return integer 
     */
    public function getCintProjectQuotaId()
    {
        return $this->cintProjectQuotaId;
    }

    /**
     * Set appMemberId
     *
     * @param string $appMemberId
     * @return CintResearchSurveyParticipationHistory
     */
    public function setAppMemberId($appMemberId)
    {
        $this->appMemberId = $appMemberId;

        return $this;
    }

    /**
     * Get appMemberId
     *
     * @return string 
     */
    public function getAppMemberId()
    {
        return $this->appMemberId;
    }

    /**
     * Set point
     *
     * @param integer $point
     * @return CintResearchSurveyParticipationHistory
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
     * Set type
     *
     * @param integer $type
     * @return CintResearchSurveyParticipationHistory
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set stashData
     *
     * @param string $stashData
     * @return CintResearchSurveyParticipationHistory
     */
    public function setStashData($stashData)
    {
        $this->stashData = $stashData;

        return $this;
    }

    /**
     * Get stashData
     *
     * @return string 
     */
    public function getStashData()
    {
        return $this->stashData;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return CintResearchSurveyParticipationHistory
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return CintResearchSurveyParticipationHistory
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
