<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserVisitLog
 *
 * @ORM\Table(name="user_visit_log", uniqueConstraints={@ORM\UniqueConstraint(name="indx_user_target_daily", columns={"user_id", "target_flag", "visit_date"})}, indexes={@ORM\Index(name="indx_user_target", columns={"user_id", "target_flag"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\UserVisitLogRepository")
 */
class UserVisitLog
{
    const TARGET_FLAG_GAME_SEEKER=3; 
    /**
     * @var integer
     *
     * @ORM\Column(name="target_flag", type="integer", nullable=true, options={"comment": "分类标志位", "default": -1})
     */
    private $targetFlag;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="visit_date", type="date", nullable=false)
     */
    private $visitDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"comment": "创建日期"})
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
        $this->setVisitDate(new \DateTime())
            ->setCreatedAt(new \DateTime() );
    }
    /**
     * Set targetFlag
     *
     * @param integer $targetFlag
     * @return UserVisitLog
     */
    public function setTargetFlag($targetFlag)
    {
        $this->targetFlag = $targetFlag;

        return $this;
    }

    /**
     * Get targetFlag
     *
     * @return integer 
     */
    public function getTargetFlag()
    {
        return $this->targetFlag;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserVisitLog
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
     * Set visitDate
     *
     * @param \DateTime $visitDate
     * @return UserVisitLog
     */
    public function setVisitDate($visitDate)
    {
        $this->visitDate = $visitDate;
        $this->visitDate->setTime(0,0);

        return $this;
    }

    /**
     * Get visitDate
     *
     * @return \DateTime 
     */
    public function getVisitDate()
    {
        return $this->visitDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserVisitLog
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
