<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyListJob
 *
 * @ORM\Table(name="survey_list_jobs")
 * @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\SurveyListJobRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SurveyListJob
{
    private static $timeslots = array(
        array('min' => '00:00:00', 'max' => '08:00:00'),
        array('min' => '08:00:01', 'max' => '16:00:00'),
        array('min' => '16:00:01', 'max' => '23:59:59')
    );

    public static function getTimeslot($time)
    {
        foreach (self::$timeslots as $timeslot) {
            if ($time >= strtotime($timeslot['min']) && $time <= strtotime($timeslot['max'])) {
                return $timeslot;
            }
        }
    }

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
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    public function __construct($userId)
    {
        $this->userId = $userId;
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
     * Set userId
     *
     * @param integer $userId
     * @return SurveyListJob
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SurveyListJob
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
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }
}
