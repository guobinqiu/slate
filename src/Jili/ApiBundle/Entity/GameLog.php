<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameLog
 *
 * @ORM\Table(name="game_log")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\GameLogRepository")
 */
class GameLog
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
     * @ORM\Column(name="point_uid", type="integer")
     */
    private $pointUid;

    /**
     * @var integer
     *
     * @ORM\Column(name="game_point", type="integer")
     */
    private $gamePoint;


    /**
     * @var string
     *
     * @ORM\Column(name="game_date", type="string", length=15)
     */
    private $gameDate;

    /**
     * @var string
     *
     * @ORM\Column(name="game_time", type="string", length=30)
     */
    private $gameTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="game_score", type="integer", nullable=true, options={"default": 0})
     */
    private $gameScore;

    /**
     * @var integer
     *
     * @ORM\Column(name="game_type", type="integer", nullable=true, options={"default": 0})
     */
    private $gameType;

    /**
     * @var integer
     *
     * @ORM\Column(name="mass_point", type="integer", nullable=true, options={"default": 0})
     */
    private $massPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="goal_point", type="integer", nullable=true, options={"default": 0})
     */
    private $goalPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="ranking_point", type="integer", nullable=true, options={"default": 0})
     */
    private $rankingPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="attendance_point", type="integer", nullable=true, options={"default": 0})
     */
    private $attendancePoint;


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
     * Set pointUid
     *
     * @param integer $pointUid
     * @return GameLog
     */
    public function setPointUid($pointUid)
    {
        $this->pointUid = $pointUid;

        return $this;
    }

    /**
     * Get pointUid
     *
     * @return integer
     */
    public function getPointUid()
    {
        return $this->pointUid;
    }


    /**
     * Set gamePoint
     *
     * @param integer $gamePoint
     * @return GameLog
     */
    public function setGamePoint($gamePoint)
    {
        $this->gamePoint = $gamePoint;

        return $this;
    }

    /**
     * Get gamePoint
     *
     * @return integer
     */
    public function getGamePoint()
    {
        return $this->gamePoint;
    }


    /**
     * Set gameDate
     *
     * @param string $gameDate
     * @return GameLog
     */
    public function setGameDate($gameDate)
    {
        $this->gameDate = $gameDate;

        return $this;
    }

    /**
     * Get gameDate
     *
     * @return string
     */
    public function getGameDate()
    {
        return $this->gameDate;
    }

   /**
     * Set gameTime
     *
     * @param string $gameTime
     * @return GameLog
     */
    public function setGameTime($gameTime)
    {
        $this->gameTime = $gameTime;

        return $this;
    }

    /**
     * Get gameTime
     *
     * @return string
     */
    public function getGameTime()
    {
        return $this->gameTime;
    }


    /**
     * Set gameScore
     *
     * @param integer $gameScore
     * @return GameLog
     */
    public function setGameScore($gameScore)
    {
        $this->gameScore = $gameScore;

        return $this;
    }

    /**
     * Get gameScore
     *
     * @return integer
     */
    public function getGameScore()
    {
        return $this->gameScore;
    }


    /**
     * Set gameType
     *
     * @param integer $gameType
     * @return GameLog
     */
    public function setGameType($gameType)
    {
        $this->gameType = $gameType;

        return $this;
    }

    /**
     * Get gameType
     *
     * @return integer
     */
    public function getGameType()
    {
        return $this->gameType;
    }


     /**
     * Set massPoint
     *
     * @param integer $massPoint
     * @return GameLog
     */
    public function setMassPoint($massPoint)
    {
        $this->massPoint = $massPoint;

        return $this;
    }

    /**
     * Get massPoint
     *
     * @return integer
     */
    public function getMassPoint()
    {
        return $this->massPoint;
    }

    /**
     * Set goalPoint
     *
     * @param integer $goalPoint
     * @return GameLog
     */
    public function setGoalPoint($goalPoint)
    {
        $this->goalPoint = $goalPoint;

        return $this;
    }

    /**
     * Get goalPoint
     *
     * @return integer
     */
    public function getGoalPoint()
    {
        return $this->goalPoint;
    }



    /**
     * Set rankingPoint
     *
     * @param integer $rankingPoint
     * @return GameLog
     */
    public function setRankingPoint($rankingPoint)
    {
        $this->rankingPoint = $rankingPoint;

        return $this;
    }

    /**
     * Get rankingPoint
     *
     * @return integer
     */
    public function getRankingPoint()
    {
        return $this->rankingPoint;
    }

    /**
     * Set attendancePoint
     *
     * @param integer $attendancePoint
     * @return GameLog
     */
    public function setAttendancePoint($attendancePoint)
    {
        $this->attendancePoint = $attendancePoint;

        return $this;
    }

    /**
     * Get attendancePoint
     *
     * @return integer
     */
    public function getAttendancePoint()
    {
        return $this->attendancePoint;
    }






}
