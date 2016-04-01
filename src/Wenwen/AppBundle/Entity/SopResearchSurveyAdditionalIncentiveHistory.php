<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SopResearchSurveyAdditionalIncentiveHistory
 *
 * @ORM\Table(name="sop_research_survey_additional_incentive_history", uniqueConstraints={@ORM\UniqueConstraint(name="hash_uniq", columns={"hash"})}, indexes={@ORM\Index(name="project_updated_idx", columns={"survey_id", "updated_at"})})
 * @ORM\Entity
 */
class SopResearchSurveyAdditionalIncentiveHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="survey_id", type="integer", nullable=false)
     */
    private $surveyId;

    /**
     * @var integer
     *
     * @ORM\Column(name="quota_id", type="integer", nullable=false)
     */
    private $quotaId;

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
     * @ORM\Column(name="hash", type="string", length=255, nullable=false)
     */
    private $hash;

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
     * Set surveyId
     *
     * @param integer $surveyId
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
     * Set appMemberId
     *
     * @param string $appMemberId
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
     * Set hash
     *
     * @param string $hash
     * @return SopResearchSurveyAdditionalIncentiveHistory
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set stashData
     *
     * @param string $stashData
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
     * @return SopResearchSurveyAdditionalIncentiveHistory
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
