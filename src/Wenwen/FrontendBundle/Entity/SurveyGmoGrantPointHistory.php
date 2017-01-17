<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyGmoGrantPointHistory
 *
 * @ORM\Table(name="survey_gmo_grant_point_history", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="survey_gmo_grant_point_history_uniq", columns={"member_id", "survey_id", "grant_times"})
 * })
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class SurveyGmoGrantPointHistory
{
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
     * @ORM\Column(name="member_id", type="integer")
     */
    private $memberId;

    /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer")
     */
    private $point;

    /**
     * @var integer
     *
     * @ORM\Column(name="survey_id", type="integer")
     */
    private $surveyId;

    /**
     * @var string
     *
     * @ORM\Column(name="survey_name", type="string", length=255)
     */
    private $surveyName;

    /**
     * 初次赠点grantTimes＝1，事后每次补点grantTimes＋1
     *
     * @var integer
     *
     * @ORM\Column(name="grant_times", type="integer")
     */
    private $grantTimes;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
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
     * Set memberId
     *
     * @param integer $memberId
     * @return SurveyGmoGrantPointHistory
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * Get memberId
     *
     * @return integer 
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * Set point
     *
     * @param integer $point
     * @return SurveyGmoGrantPointHistory
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
     * Set surveyId
     *
     * @param integer $surveyId
     * @return SurveyGmoGrantPointHistory
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
     * Set surveyName
     *
     * @param string $surveyName
     * @return SurveyGmoGrantPointHistory
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = $surveyName;

        return $this;
    }

    /**
     * Get surveyName
     *
     * @return string 
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     * Set grantTimes
     *
     * @param integer $grantTimes
     * @return SurveyGmoGrantPointHistory
     */
    public function setGrantTimes($grantTimes)
    {
        $this->grantTimes = $grantTimes;

        return $this;
    }

    /**
     * Get grantTimes
     *
     * @return integer 
     */
    public function getGrantTimes()
    {
        return $this->grantTimes;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return SurveyGmoGrantPointHistory
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
