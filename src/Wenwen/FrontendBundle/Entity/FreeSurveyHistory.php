<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FreeSurveyHistory
 * 记录第三方用问卷回答信息
 *
 * @ORM\Table(name="free_survey_history")
* @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\FreeSurveyHistoryRepository")
 */
class FreeSurveyHistory
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
     * @var string
     *
     * @ORM\Column(name="ukey", type="string", length=30, nullable=false)
     */
    private $uKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="partner_id", type="integer", nullable=false)
     */
    private $partnerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="project_id", type="integer", nullable=false)
     */
    private $projectId;

    /**
     * @var string
     *
     * @ORM\Column(name="survey_url", type="string", length=255, nullable=false)
     */
    private $surveyUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=10, nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param string $uKey
     */
    public function setUKey($uKey)
    {
        $this->uKey = $uKey;
    }

    /**
     *
     * @return string
     */
    public function getUKey()
    {
        return $this->uKey;
    }

    /**
     *
     * @param integer $partnerId
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
    }

    /**
     *
     * @return integer
     */
    public function getPartnerId()
    {
        return $this->partnerId;
    }

    /**
     *
     * @param integer $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     *
     * @return integer
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     *
     * @param string $surveyUrl
     */
    public function setSurveyUrl($surveyUrl)
    {
        $this->surveyUrl = $surveyUrl;
    }

    /**
     *
     * @return string
     */
    public function getSurveyUrl()
    {
        return $this->surveyUrl;
    }

    /**
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


}
