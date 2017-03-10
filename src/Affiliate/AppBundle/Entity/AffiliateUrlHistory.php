<?php

namespace Affiliate\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AffiliateUrlHistory
 * 记录第三方用问卷回答信息
 *
 * @ORM\Table(name="affiliate_url_history")
 * @ORM\Entity(repositoryClass="Affiliate\AppBundle\Repository\AffiliateUrlHistoryRepository")
 */
class AffiliateUrlHistory
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
     * @ORM\Column(name="ukey", type="string", length=30, nullable=false, options={"comment": "First column of url files(uploaded)"})
     */
    private $uKey;

    /**
     * @ORM\ManyToOne(targetEntity="AffiliateProject")
     * @ORM\JoinColumn(name="affiliate_project_id", referencedColumnName="id", nullable=false)
     */
    private $affiliateProject;

    /**
     * @var string
     *
     * @ORM\Column(name="survey_url", type="string", length=255, nullable=false, options={"comment": "Second column of url files(uploaded)"})
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
     * @param AffiliateProject $affiliateProject
     */
    public function setAffiliateProject($affiliateProject)
    {
        $this->affiliateProject = $affiliateProject;
    }

    /**
     *
     * @return AffiliateProject
     */
    public function getAffiliateProject()
    {
        return $this->affiliateProject;
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