<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FulcrumResearchSurveyParticipationHistory
 *
 * @ORM\Table(name="fulcrum_research_survey_participation_history", uniqueConstraints={@ORM\UniqueConstraint(name="fulcrum_project_member_uniq", columns={"fulcrum_project_id", "app_member_id"})})
 * @ORM\Entity
 */
class FulcrumResearchSurveyParticipationHistory
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
     * @var integer
     *
     * @ORM\Column(name="fulcrum_project_id", type="integer")
     */
    private $fulcrumProjectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="fulcrum_project_quota_id", type="integer")
     */
    private $fulcrumProjectQuotaId;

    /**
     * @var string
     *
     * @ORM\Column(name="app_member_id", type="string", length=255)
     */
    private $appMemberId;

    /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer", options={"default": 0})
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

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setPoint(0);
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
     * Set fulcrumProjectId
     *
     * @param integer $fulcrumProjectId
     * @return FulcrumResearchSurveyParticipationHistory
     */
    public function setFulcrumProjectId($fulcrumProjectId)
    {
        $this->fulcrumProjectId = $fulcrumProjectId;

        return $this;
    }

    /**
     * Get fulcrumProjectId
     *
     * @return integer
     */
    public function getFulcrumProjectId()
    {
        return $this->fulcrumProjectId;
    }

    /**
     * Set fulcrumProjectQuotaId
     *
     * @param integer $fulcrumProjectQuotaId
     * @return FulcrumResearchSurveyParticipationHistory
     */
    public function setFulcrumProjectQuotaId($fulcrumProjectQuotaId)
    {
        $this->fulcrumProjectQuotaId = $fulcrumProjectQuotaId;

        return $this;
    }

    /**
     * Get fulcrumProjectQuotaId
     *
     * @return integer
     */
    public function getFulcrumProjectQuotaId()
    {
        return $this->fulcrumProjectQuotaId;
    }

    /**
     * Set appMemberId
     *
     * @param string $appMemberId
     * @return FulcrumResearchSurveyParticipationHistory
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
     * @return FulcrumResearchSurveyParticipationHistory
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
     * @return FulcrumResearchSurveyParticipationHistory
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
     * @return FulcrumResearchSurveyParticipationHistory
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
     * @return FulcrumResearchSurveyParticipationHistory
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
     * @return FulcrumResearchSurveyParticipationHistory
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
}
