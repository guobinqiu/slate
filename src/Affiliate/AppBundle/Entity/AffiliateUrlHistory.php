<?php

namespace Affiliate\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AffiliateUrlHistory
 * 记录第三方用问卷回答信息
 *
 * @ORM\Table(name="affiliate_url_history", indexes={@ORM\Index(name="index_projectI_id", columns={"project_id"})})
 * @ORM\Entity(repositoryClass="Affiliate\AppBundle\Repository\AffiliateUrlHistoryRepository")
 */
class AffiliateUrlHistory
{

    // status 的可用状态值
    const SURVEY_STATUS_INIT      = 'init'; // url的初始状态，被导入的时候设置
    const SURVEY_STATUS_FORWARD   = 'forward'; // 有用户点击后，被分配出去的url，不能再次被分配
    const SURVEY_STATUS_COMPLETE  = 'complete'; // 用户完成，triples那边回调后更新（暂时没做）
    const SURVEY_STATUS_SCREENOUT = 'screenout'; // 用户screenout，triples那边回调后更新（暂时没做）
    const SURVEY_STATUS_QUOTAFULL = 'quotafull'; // 用户quotafull，triples那边回调后更新（暂时没做）
    const SURVEY_STATUS_ERROR     = 'error'; // 用户可能已经在别处回答过问卷了，客户那边直接拒绝（暂时没做）

    /**
     * @var integer
     *
     * @ORM\Column(name="url_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $urlId;

    /**
     * @var string
     *
     * @ORM\Column(name="ukey", type="string", length=30, nullable=false, options={"comment": "First column of url files(uploaded)"})
     */
    private $uKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="project_id", type="integer", nullable=false)
     */
    private $projectId;

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
    public function getUrlId()
    {
        return $this->urlId;
    }

    /**
     *
     * @param
     */
    public function setUrlId($urlId)
    {
        $this->urlId = $urlId;
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
