<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="survey_partner")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\SurveyPartnerRepository")
 * 
 */
class SurveyPartner
{
    const STATUS_INIT = 'init';
    const STATUS_OPEN = 'open';
    const STATUS_CLOSE = 'close';

    const GENDER_BOTH = 'both';
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const PARTNER_TRIPLES = 'triples';
    const PARTNER_FORSURVEY = 'forsurvey';

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
     * @ORM\Column(name="partner_name", type="string", length=32, nullable=false)
     * @Assert\NotBlank(message = "Please fill out partnerId")
     * @Assert\Choice(
     *      choices = {"triples", "forsurvey"},
     *      message = "这不是一个有效的partner name"
     * )
     */
    private $partnerName;

    /**
     * @var string
     * @ORM\Column(name="survey_id", type="string", length=32, nullable=false)
     * @Assert\Length(
     *      min=1,
     *      max=32,
     *      minMessage = "对方系统的问卷编号为1-32个字符",
     *      maxMessage = "对方系统的问卷编号为1-32个字符"
     * )
     * @Assert\NotBlank(
     *      message = "请输入对方系统的问卷编号"
     * )
     */
    private $surveyId;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      min=1,
     *      max=255,
     *      minMessage = "url的长度不能小于1字节",
     *      maxMessage = "url的长度不能超过255字节"
     * )
     * @Assert\NotBlank(
     *      message = "请输入url"
     * )
     * @Assert\Url(
     *      message = "请输入合法的url"
     * )
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     * @Assert\Length(
     *      min=1,
     *      max=100,
     *      minMessage = "问卷标题为1-100个字符",
     *      maxMessage = "问卷标题为1-100个字符"
     * )
     * @Assert\NotBlank(
     *      message = "请输入问卷标题"
     * )
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="content", type="text", nullable=true)
     * @Assert\Length(
     *      min=1,
     *      max=300,
     *      minMessage = "问卷提示内容为1-300个字符",
     *      maxMessage = "问卷提示内容为1-300个字符"
     * )
     */
    private $content;

    /**
     * @var boolean
     * @ORM\Column(name="reentry", type="boolean", nullable=false)
     */
    private $reentry;

    /**
     * @var int
     * @ORM\Column(name="loi", type="integer", nullable=false)
     * @Assert\Range(
     *      min=1,
     *      max=60,
     *      minMessage = "问卷时长为1-60分钟",
     *      maxMessage = "问卷时长为1-60分钟"
     * )
     * @Assert\NotBlank(
     *      message = "请输入问卷所需时长（预估）"
     * )
     */
    private $loi;

    /**
     * @var int
     * @ORM\Column(name="ir", type="integer", nullable=false)
     * @Assert\Range(
     *      min=1,
     *      max=100,
     *      minMessage = "问卷通过率为1%-100%",
     *      maxMessage = "问卷通过率为1%-100%"
     * )
     * @Assert\NotBlank(
     *      message = "请输入问卷通过率（预估）"
     * )
     */
    private $ir;

    /**
     * @var int
     * @ORM\Column(name="complete_point", type="integer", nullable=false)
     * @Assert\Range(
     *      min=100,
     *      max=5000,
     *      minMessage = "complete积分最少为100",
     *      maxMessage = "complete积分最多为5000"
     * )
     * @Assert\NotBlank(
     *      message = "请输入complete积分"
     * )
     */
    private $completePoint;

    /**
     * @var int
     * @ORM\Column(name="screenout_point", type="integer", nullable=false)
     * @ORM\Column(name="complete_point", type="integer", nullable=false)
     * @Assert\Range(
     *      min=1,
     *      max=100,
     *      minMessage = "screenout积分最少为1",
     *      maxMessage = "screenout积分最多为100"
     * )
     * @Assert\NotBlank(
     *      message = "请输入screenout积分"
     * )
     */
    private $screenoutPoint;

    /**
     * @var int
     * @ORM\Column(name="quotafull_point", type="integer", nullable=false)
     * @Assert\Range(
     *      min=1,
     *      max=100,
     *      minMessage = "quotaful积分最少为1",
     *      maxMessage = "quotaful积分最多为100"
     * )
     * @Assert\NotBlank(
     *      message = "请输入quotaful积分"
     * )
     */
    private $quotafullPoint;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=10, nullable=false)
     * @Assert\Choice(
     *      choices = {"init", "open", "close"},
     *      message = "这不是一个有效的status"
     * )
     */
    private $status;

    /**
     * @var int
     * @ORM\Column(name="min_age", type="integer", nullable=false)
     * @Assert\Range(
     *      min=0,
     *      max=100,
     *      minMessage = "最小年龄最少为0岁",
     *      maxMessage = "最小年龄最少为100岁"
     * )
     * @Assert\NotBlank(
     *      message = "请输入最小年龄"
     * )
     */
    private $minAge;

    /**
     * @var int
     * @ORM\Column(name="max_age", type="integer", nullable=false)
     * @Assert\Range(
     *      min=0,
     *      max=150,
     *      minMessage = "最大年龄最少为0岁",
     *      maxMessage = "最大年龄最少为150岁"
     * )
     * @Assert\NotBlank(
     *      message = "请输入最小年龄"
     * )
     */
    private $maxAge;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string", length=10, nullable=false)
     * @Assert\Choice(
     *      choices = {"both", "male", "female"},
     *      message = "这不是一个有效的性别"
     * )
     */
    private $gender;

    /**
     * @var string
     * @ORM\Column(name="province", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min=1,
     *      max=300,
     *      minMessage = "允许参与的省份一览为1-300个字符",
     *      maxMessage = "允许参与的省份一览为1-300个字符"
     * )
     */
    private $province;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min=1,
     *      max=300,
     *      minMessage = "允许参与的城市一览为1-300个字符",
     *      maxMessage = "允许参与的城市一览为1-300个字符"
     * )
     */
    private $city;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="SurveyPartnerParticipationHistory", mappedBy="surveyPartner")
     */
    private $surveyPartnerParticipationHistorys;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPartnerName($partnerName)
    {
        $this->partnerName = $partnerName;
    }

    public function getPartnerName()
    {
        return $this->partnerName;
    }

    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    public function getSurveyId()
    {
        return $this->surveyId;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setReentry($reentry)
    {
        $this->reentry = $reentry;
    }

    public function getReentry()
    {
        return $this->reentry;
    }

    public function setLoi($loi)
    {
        $this->loi = $loi;
    }

    public function getLoi()
    {
        return $this->loi;
    }

    public function setIr($ir)
    {
        $this->ir = $ir;
    }

    public function getIr()
    {
        return $this->ir;
    }

    public function setCompletePoint($completePoint)
    {
        $this->completePoint = $completePoint;
    }

    public function getCompletePoint()
    {
        return $this->completePoint;
    }

    public function setScreenoutPoint($screenoutPoint)
    {
        $this->screenoutPoint = $screenoutPoint;
    }

    public function getScreenoutPoint()
    {
        return $this->screenoutPoint;
    }

    public function setQuotafullPoint($quotafullPoint)
    {
        $this->quotafullPoint = $quotafullPoint;
    }

    public function getQuotafullPoint()
    {
        return $this->quotafullPoint;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setMinAge($minAge)
    {
        $this->minAge = $minAge;
    }

    public function getMinAge()
    {
        return $this->minAge;
    }

    public function setMaxAge($maxAge)
    {
        $this->maxAge = $maxAge;
    }

    public function getMaxAge()
    {
        return $this->maxAge;
    }

    public function setGender($gender)
    {
        if (!in_array($gender, 
                    array(
                        self::GENDER_BOTH, 
                        self::GENDER_MALE, 
                        self::GENDER_FEMALE, 
                        )
                    )
            ) 
        {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->gender = $gender;
    }

    public function getGender()
    {
        return $this->gender;
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

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getSurveyPartnerParticipationHistorys()
    {
        return $this->surveyPartnerParticipationHistorys;
    }

    public function __toString() {
            return "id is: {$this->id}, surveyId is {$this->surveyId}";
    }

}
