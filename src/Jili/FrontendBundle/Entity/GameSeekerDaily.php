<?php
namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameSeekerDaily
 *
 * @ORM\Table(name="game_seeker_daily", uniqueConstraints={@ORM\UniqueConstraint(name="token", columns={"token"}), @ORM\UniqueConstraint(name="uid_daily", columns={"user_id", "clicked_day"})})
 *
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GameSeekerDailyRepository")
 */
class GameSeekerDaily
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clicked_day", type="date", nullable=true)
     */
    private $clickedDay;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=32, nullable=false, unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="token_updated_at", type="datetime", nullable=false)
     */
    private $tokenUpdatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function __construct() 
    {
        $this->setPoints( -1 );
        $this->setTokenUpdatedAt(new \DateTime());
        $this->setCreatedAt(new \DateTime() );

        $this->setClickedDay(new \DateTime());
    }


    /**
     * Set userId
     *
     * @param integer $userId
     * @return GameSeekerDaily
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
     * Set points
     *
     * @param integer $points
     * @return GameSeekerDaily
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set clickedDay
     *
     * @param \DateTime $clickedDay
     * @return GameSeekerDaily
     */
    public function setClickedDay($clickedDay)
    {
        $this->clickedDay= $clickedDay;
        $this->clickedDay->setTime(0,0);

        return $this;
    }

    /**
     * Get clickedDay
     *
     * @return \DateTime 
     */
    public function getClickedDay()
    {
        return $this->clickedDay;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return GameSeekerDaily
     */
    public function setToken($token = '')
    {
        $updatedAtPrevious = $this->getTokenUpdatedAt();
        $ts_now = time();

        if(strlen($token) !== 32 ) {
            $token = md5($token . $this->getUserId() . $updatedAtPrevious->getTimestamp() . $ts_now );
        } 
        $this->token = $token;

        $dateTime = new \DateTime() ;
        $dateTime->setTimestamp($ts_now);
        $this->setTokenUpdatedAt($dateTime);

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tokenUpdatedAt
     *
     * @param \DateTime $tokenUpdatedAt
     * @return GameSeekerDaily
     */
    public function setTokenUpdatedAt($tokenUpdatedAt)
    {
        $this->tokenUpdatedAt = $tokenUpdatedAt;

        return $this;
    }

    /**
     * Get tokenUpdatedAt
     *
     * @return \DateTime 
     */
    public function getTokenUpdatedAt()
    {
        return $this->tokenUpdatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return GameSeekerDaily
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
