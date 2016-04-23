<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckinPointTimes
 *
 * @ORM\Table(name="checkin_point_times")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\CheckinPointTimesRepository")
 */
class CheckinPointTimes
{

    public function __construct()
    {
        $this->createTime = new \DateTime();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="point_times", type="integer")
     */
    private $pointTimes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime")
     */
    private $endTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="checkin_type", type="boolean", options={"default": 1})
     */
    private $checkinType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;


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
     * Set pointTimes
     *
     * @param integer $pointTimes
     * @return CheckinPointTimes
     */
    public function setPointTimes($pointTimes)
    {
        $this->pointTimes = $pointTimes;

        return $this;
    }

    /**
     * Get pointTimes
     *
     * @return integer
     */
    public function getPointTimes()
    {
        return $this->pointTimes;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return CheckinPointTimes
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return CheckinPointTimes
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set checkinType
     *
     * @param integer $checkinType
     * @return CheckinPointTimes
     */
    public function setCheckinType($checkinType)
    {
        $this->checkinType = $checkinType;

        return $this;
    }

    /**
     * Get checkinType
     *
     * @return integer
     */
    public function getCheckinType()
    {
        return $this->checkinType;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return CheckinPointTimes
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }





}
