<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SopResearchSurveyParticipationHistory
 *
 * @ORM\Table(name="sop_research_survey_participation_history", uniqueConstraints={@ORM\UniqueConstraint(name="project_app_member_uniq", columns={"partner_app_project_id", "app_member_id"})}, indexes={@ORM\Index(name="project_updated_idx", columns={"partner_app_project_id", "updated_at"})})
 * @ORM\Entity
 */
class SopResearchSurveyParticipationHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="partner_app_project_id", type="integer", nullable=false)
     */
    private $partnerAppProjectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="partner_app_project_quota_id", type="integer", nullable=false)
     */
    private $partnerAppProjectQuotaId;

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

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set partnerAppProjectId
     *
     * @param integer $partnerAppProjectId
     * @return SopResearchSurveyParticipationHistory
     */
    public function setPartnerAppProjectId($partnerAppProjectId)
    {
        $this->partnerAppProjectId = $partnerAppProjectId;

        return $this;
    }

    /**
     * Get partnerAppProjectId
     *
     * @return integer
     */
    public function getPartnerAppProjectId()
    {
        return $this->partnerAppProjectId;
    }

    /**
     * Set partnerAppProjectQuotaId
     *
     * @param integer $partnerAppProjectQuotaId
     * @return SopResearchSurveyParticipationHistory
     */
    public function setPartnerAppProjectQuotaId($partnerAppProjectQuotaId)
    {
        $this->partnerAppProjectQuotaId = $partnerAppProjectQuotaId;

        return $this;
    }

    /**
     * Get partnerAppProjectQuotaId
     *
     * @return integer
     */
    public function getPartnerAppProjectQuotaId()
    {
        return $this->partnerAppProjectQuotaId;
    }

    /**
     * Set appMemberId
     *
     * @param string $appMemberId
     * @return SopResearchSurveyParticipationHistory
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
     * @return SopResearchSurveyParticipationHistory
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
     * @return SopResearchSurveyParticipationHistory
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
     * @return SopResearchSurveyParticipationHistory
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
     * @return SopResearchSurveyParticipationHistory
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
     * @return SopResearchSurveyParticipationHistory
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
