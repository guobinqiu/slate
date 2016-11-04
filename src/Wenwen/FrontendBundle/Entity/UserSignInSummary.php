<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSignInSummary
 *
 * @ORM\Table(name="user_sign_in_summary")
 * @ORM\Entity
 */
class UserSignInSummary
{
    /**
     * 最大连续签到天数
     */
    const MAX_CONSECUTIVE_DAYS = 5;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="userSignInSummary")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_sign_in_count", type="integer")
     */
    private $totalSignInCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="consecutive_days", type="integer")
     */
    private $consecutiveDays;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     */
    private $startDate;


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
     * Set user
     *
     * @param User $user
     * @return UserSignInSummary
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set totalSignInCount
     *
     * @param integer $totalSignInCount
     * @return UserSignInSummary
     */
    public function setTotalSignInCount($totalSignInCount)
    {
        $this->totalSignInCount = $totalSignInCount;

        return $this;
    }

    /**
     * Get totalSignInCount
     *
     * @return integer 
     */
    public function getTotalSignInCount()
    {
        return $this->totalSignInCount;
    }

    /**
     * Set consecutiveDays
     *
     * @param integer $consecutiveDays
     * @return UserSignInSummary
     */
    public function setConsecutiveDays($consecutiveDays)
    {
        $this->consecutiveDays = $consecutiveDays;

        return $this;
    }

    /**
     * Get consecutiveDays
     *
     * @return integer 
     */
    public function getConsecutiveDays()
    {
        return $this->consecutiveDays;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return UserSignInSummary
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
}
