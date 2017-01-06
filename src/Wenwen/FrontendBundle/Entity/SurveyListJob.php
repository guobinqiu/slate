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
//        array('min' => '00:00:00', 'max' => '08:00:00'),
//        array('min' => '08:00:01', 'max' => '16:00:00'),
//        array('min' => '16:00:01', 'max' => '23:59:59'),
        array('min' => '00:00:00', 'max' => '01:00:00'),
        array('min' => '01:00:01', 'max' => '02:00:00'),
        array('min' => '02:00:01', 'max' => '03:00:00'),
        array('min' => '03:00:01', 'max' => '04:00:00'),
        array('min' => '04:00:01', 'max' => '05:00:00'),
        array('min' => '05:00:01', 'max' => '06:00:00'),
        array('min' => '06:00:01', 'max' => '07:00:00'),
        array('min' => '07:00:01', 'max' => '08:00:00'),
        array('min' => '08:00:01', 'max' => '08:10:00'),
        array('min' => '08:10:01', 'max' => '08:20:00'),
        array('min' => '08:20:01', 'max' => '08:30:00'),
        array('min' => '08:30:01', 'max' => '08:40:00'),
        array('min' => '08:40:01', 'max' => '08:50:00'),
        array('min' => '08:50:01', 'max' => '09:00:00'),
        array('min' => '09:00:01', 'max' => '09:10:00'),
        array('min' => '09:10:01', 'max' => '09:20:00'),
        array('min' => '09:20:01', 'max' => '09:30:00'),
        array('min' => '09:30:01', 'max' => '09:40:00'),
        array('min' => '09:40:01', 'max' => '09:50:00'),
        array('min' => '09:50:01', 'max' => '10:00:00'),
        array('min' => '10:00:01', 'max' => '10:10:00'),
        array('min' => '10:10:01', 'max' => '10:20:00'),
        array('min' => '10:20:01', 'max' => '10:30:00'),
        array('min' => '10:30:01', 'max' => '10:40:00'),
        array('min' => '10:40:01', 'max' => '10:50:00'),
        array('min' => '10:50:01', 'max' => '11:00:00'),
        array('min' => '11:00:01', 'max' => '11:10:00'),
        array('min' => '11:10:01', 'max' => '11:20:00'),
        array('min' => '11:20:01', 'max' => '11:30:00'),
        array('min' => '11:30:01', 'max' => '11:40:00'),
        array('min' => '11:40:01', 'max' => '11:50:00'),
        array('min' => '11:50:01', 'max' => '12:00:00'),
        array('min' => '12:00:01', 'max' => '12:10:00'),
        array('min' => '12:10:01', 'max' => '12:20:00'),
        array('min' => '12:20:01', 'max' => '12:30:00'),
        array('min' => '12:30:01', 'max' => '12:40:00'),
        array('min' => '12:40:01', 'max' => '12:50:00'),
        array('min' => '12:50:01', 'max' => '13:00:00'),
        array('min' => '13:00:01', 'max' => '13:10:00'),
        array('min' => '13:10:01', 'max' => '13:20:00'),
        array('min' => '13:20:01', 'max' => '13:30:00'),
        array('min' => '13:30:01', 'max' => '13:40:00'),
        array('min' => '13:40:01', 'max' => '13:50:00'),
        array('min' => '13:50:01', 'max' => '14:00:00'),
        array('min' => '14:00:01', 'max' => '14:10:00'),
        array('min' => '14:10:01', 'max' => '14:20:00'),
        array('min' => '14:20:01', 'max' => '14:30:00'),
        array('min' => '14:30:01', 'max' => '14:40:00'),
        array('min' => '14:40:01', 'max' => '14:50:00'),
        array('min' => '14:50:01', 'max' => '15:00:00'),
        array('min' => '15:00:01', 'max' => '15:10:00'),
        array('min' => '15:10:01', 'max' => '15:20:00'),
        array('min' => '15:20:01', 'max' => '15:30:00'),
        array('min' => '15:30:01', 'max' => '15:40:00'),
        array('min' => '15:40:01', 'max' => '15:50:00'),
        array('min' => '15:50:01', 'max' => '16:00:00'),
        array('min' => '16:00:01', 'max' => '16:10:00'),
        array('min' => '16:10:01', 'max' => '16:20:00'),
        array('min' => '16:20:01', 'max' => '16:30:00'),
        array('min' => '16:30:01', 'max' => '16:40:00'),
        array('min' => '16:40:01', 'max' => '16:50:00'),
        array('min' => '16:50:01', 'max' => '17:00:00'),
        array('min' => '17:00:01', 'max' => '17:10:00'),
        array('min' => '17:10:01', 'max' => '17:20:00'),
        array('min' => '17:20:01', 'max' => '17:30:00'),
        array('min' => '17:30:01', 'max' => '17:40:00'),
        array('min' => '17:40:01', 'max' => '17:50:00'),
        array('min' => '17:50:01', 'max' => '18:00:00'),
        array('min' => '18:00:01', 'max' => '18:10:00'),
        array('min' => '18:10:01', 'max' => '18:20:00'),
        array('min' => '18:20:01', 'max' => '18:30:00'),
        array('min' => '18:30:01', 'max' => '18:40:00'),
        array('min' => '18:40:01', 'max' => '18:50:00'),
        array('min' => '18:50:01', 'max' => '19:00:00'),
        array('min' => '19:00:01', 'max' => '19:10:00'),
        array('min' => '19:10:01', 'max' => '19:20:00'),
        array('min' => '19:20:01', 'max' => '19:30:00'),
        array('min' => '19:30:01', 'max' => '19:40:00'),
        array('min' => '19:40:01', 'max' => '19:50:00'),
        array('min' => '19:50:01', 'max' => '20:00:00'),
        array('min' => '20:00:01', 'max' => '21:00:00'),
        array('min' => '21:00:01', 'max' => '22:00:00'),
        array('min' => '22:00:01', 'max' => '23:00:00'),
        array('min' => '23:00:01', 'max' => '23:59:59'),
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
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
