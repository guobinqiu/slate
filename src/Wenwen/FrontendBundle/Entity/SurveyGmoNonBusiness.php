<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Symfony\Component\Validator\Constraints as Assert;
use Wenwen\AppBundle\Validator\Constraints as MyAssert;

/**
 * SurveyGmoNonBusiness
 *
 * @ORM\Table(name="survey_gmo_non_business")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class SurveyGmoNonBusiness
{
    const TYPE_GK = '属性调查';
    const TYPE_SS = '自主调查';
    const TYPE_BUSINESS = '通常调查';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="research_id", type="integer", unique=true)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=8)
     */
    private $researchId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=25)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="complete_point", type="integer")
     *
     * @Assert\NotBlank
     * @MyAssert\GreaterThanOrEqual(0)
     */
    private $completePoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="screenout_point", type="integer")
     *
     * @Assert\NotBlank
     * @MyAssert\GreaterThanOrEqual(0)
     */
    private $screenoutPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="quotafull_point", type="integer")
     *
     * @Assert\NotBlank
     * @MyAssert\GreaterThanOrEqual(0)
     */
    private $quotafullPoint;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @param $answerStatus
     * @return int
     */
    public function getPoints($answerStatus)
    {
        $points = null;
        if ($answerStatus == SurveyStatus::STATUS_COMPLETE) {
            $points = $this->getCompletePoint();
        } elseif ($answerStatus == SurveyStatus::STATUS_SCREENOUT) {
            $points = $this->getScreenoutPoint();
        } elseif ($answerStatus == SurveyStatus::STATUS_QUOTAFULL) {
            $points = $this->getQuotafullPoint();
        }
        return $points == null ? 0 : $points;
    }

    /**
     * @Assert\True(message = "screenoutPoint shouldn't greater than completePoint")
     */
    public function isScreenoutPointLegal()
    {
        return $this->screenoutPoint <= $this->completePoint;
    }

    /**
     * @Assert\True(message = "quotafullPoint shouldn't greater than completePoint")
     */
    public function isQuotafullPointLegal()
    {
        return $this->quotafullPoint <= $this->completePoint;
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
     * Set researchId
     *
     * @param integer $researchId
     * @return SurveyGmoNonBusiness
     */
    public function setResearchId($researchId)
    {
        $this->researchId = $researchId;

        return $this;
    }

    /**
     * Get researchId
     *
     * @return integer 
     */
    public function getResearchId()
    {
        return $this->researchId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SurveyGmoNonBusiness
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SurveyGmoNonBusiness
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set completePoint
     *
     * @param integer $completePoint
     * @return SurveyGmoNonBusiness
     */
    public function setCompletePoint($completePoint)
    {
        $this->completePoint = $completePoint;

        return $this;
    }

    /**
     * Get completePoint
     *
     * @return integer 
     */
    public function getCompletePoint()
    {
        return $this->completePoint;
    }

    /**
     * Set screenoutPoint
     *
     * @param integer $screenoutPoint
     * @return SurveyGmoNonBusiness
     */
    public function setScreenoutPoint($screenoutPoint)
    {
        $this->screenoutPoint = $screenoutPoint;

        return $this;
    }

    /**
     * Get screenoutPoint
     *
     * @return integer 
     */
    public function getScreenoutPoint()
    {
        return $this->screenoutPoint;
    }

    /**
     * Set quotafullPoint
     *
     * @param integer $quotafullPoint
     * @return SurveyGmoNonBusiness
     */
    public function setQuotafullPoint($quotafullPoint)
    {
        $this->quotafullPoint = $quotafullPoint;

        return $this;
    }

    /**
     * Get quotafullPoint
     *
     * @return integer 
     */
    public function getQuotafullPoint()
    {
        return $this->quotafullPoint;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SurveyGmoNonBusiness
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return SurveyGmoNonBusiness
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
}
