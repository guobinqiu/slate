<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="survey_partner",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="unique_partner_name_and_survey_id", columns={"partner_name", "survey_id"})})
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
     */
    private $partnerName;

    /**
     * @var string
     * @ORM\Column(name="survey_id", type="string", length=32, nullable=false)
     */
    private $surveyId;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="content", type="text", nullable=true)
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
     */
    private $loi;

    /**
     * @var int
     * @ORM\Column(name="ir", type="integer", nullable=false)
     */
    private $ir;

    /**
     * @var int
     * @ORM\Column(name="complete_point", type="integer", nullable=false)
     */
    private $completePoint;

    /**
     * @var int
     * @ORM\Column(name="screenout_point", type="integer", nullable=false)
     */
    private $screenoutPoint;

    /**
     * @var int
     * @ORM\Column(name="quotafull_point", type="integer", nullable=false)
     */
    private $quotafullPoint;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=10, nullable=false)
     */
    private $status;

    /**
     * @var int
     * @ORM\Column(name="min_age", type="integer", nullable=false)
     */
    private $minAge;

    /**
     * @var int
     * @ORM\Column(name="max_age", type="integer", nullable=false)
     */
    private $maxAge;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string", length=10, nullable=false)
     */
    private $gender;

    /**
     * @var string
     * @ORM\Column(name="province", type="string", length=255, nullable=true)
     */
    private $province;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
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
        if (!in_array($status, 
                    array(
                        self::STATUS_INIT, 
                        self::STATUS_OPEN, 
                        self::STATUS_CLOSE, 
                        )
                    )
            ) 
        {
            throw new \InvalidArgumentException("Invalid status");
        }
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
