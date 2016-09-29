<?php

namespace Affiliate\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AffiliateProject
 * 记录第三方用问卷项目信息
 *
 * @ORM\Table(name="affiliate_project")
 * @ORM\Entity(repositoryClass="Affiliate\AppBundle\Repository\AffiliateProjectRepository")
 */
class AffiliateProject
{

    // status 的可用状态值
    const PROJECT_STATUS_INIT      = 'init'; // 新项目，正在等待url文件上传结束
    const PROJECT_STATUS_OPEN      = 'open'; // 执行中
    const PROJECT_STATUS_CLOSE     = 'close';// 结束

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "One projectId for one Redirect url file"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AffiliatePartner")
     * @ORM\JoinColumn(name="affiliate_partner_id", referencedColumnName="id", nullable=false)
     */
    private $affiliatePartner;

    /**
     * RFQ # of tripleS
     * @var integer
     *
     * @ORM\Column(name="rfq_id", type="integer", nullable=false, options={"comment": "RFQ # from TripleS. One RFQId => multiple projectId"})
     */
    private $RFQId;

    /**
     * url for partner to access
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true, options={"comment": "url for partner to access"})
     */
    private $url;

    /**
     * File name of redirector url
     * @var string
     *
     * @ORM\Column(name="original_file_name", type="string", length=50, nullable=false, unique=true, options={"comment": "Uploaded file name"})
     */
    private $originalFileName;

    /**
     * Real path of the uploaded file on server
     * @var string
     *
     * @ORM\Column(name="real_full_path", type="string", length=255, nullable=false, options={"comment": "Real full path of the url file on the server"})
     */
    private $realFullPath;

    /**
     * @var integer
     *
     * @ORM\Column(name="init_num", type="integer", nullable=false, options={"default": 0, "comment": "The num of usable url with status init"})
     */
    private $initNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="complete_points", type="integer", nullable=false, options={"default": 0, "comment": "The reward point for compete after register"})
     */
    private $completePoints;

    /**
     * upload status
     *        init: file of url is uploading
     *        open: file uploading is finished and is opened for partner to use
     *        close: this project is closed or the urls of this file is all used by click
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=10, nullable=false, options={"comment": "init/open/close"})
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

    /**
     * Province to access
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=255, nullable=true, options={"comment": "province to access"})
     */
    private $province;
   
    /**
     * City to access
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true, options={"comment": "city to access"})
     */ 

    private $city;

    public function __construct()
    {
        $this->initNum = 0;
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
     * @param AffiliatePartner $affiliatePartner
     */
    public function setAffiliatePartner($affiliatePartner)
    {
        $this->affiliatePartner = $affiliatePartner;
    }

    /**
     *
     * @return integer
     */
    public function getAffiliatePartner()
    {
        return $this->affiliatePartner;
    }

    /**
     *
     * @param integer $RFQId
     */
    public function setRFQId($RFQId)
    {
        $this->RFQId = $RFQId;
    }

    /**
     *
     * @return integer
     */
    public function getRFQId()
    {
        return $this->RFQId;
    }

    /**
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param string $originalFileName
     */
    public function setOriginalFileName($originalFileName)
    {
        $this->originalFileName = $originalFileName;
    }

    /**
     *
     * @return string
     */
    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

    /**
     *
     * @param string $realFullPath
     */
    public function setRealFullPath($realFullPath)
    {
        $this->realFullPath = $realFullPath;
    }

    /**
     *
     * @return string
     */
    public function getRealFullPath()
    {
        return $this->realFullPath;
    }

    /**
     *
     * @param integer $initNum
     */
    public function setInitNum($initNum)
    {
        $this->initNum = $initNum;
    }

    /**
     *
     * @return integer
     */
    public function getInitNum()
    {
        return $this->initNum;
    }

    /**
     *
     * @param integer $initNum
     */
    public function setCompletePoints($completePoints)
    {
        $this->completePoints = $completePoints;
    }

    /**
     *
     * @return integer
     */
    public function getCompletePoints()
    {
        return $this->completePoints;
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

    /**
     *
     */
    public function minusInitNum()
    {
        $this->initNum --;
    }
 
    public function setProvince($province)
    {
        $this->province = $province;
    }
       
    public function getProvince()
    {
        return $this->province;
    }
  
    public function setCity($city)
    {
        $this->city = $city;
    }
  
    public function getCity()
    {
        return $this->city;
    }
}
