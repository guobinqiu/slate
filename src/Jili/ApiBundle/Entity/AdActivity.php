<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdActivity
 *
 * @ORM\Table(name="ad_activity")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdActivityRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 */
class AdActivity
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=127, nullable=true, options={"comment": "活动标题"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true, options={"comment": "活动内容描述"})
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started_at", type="datetime", options={"comment": "活动开始时间"})
     */
    private $startedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime", options={"comment": "结束时间"})
     */
    private $finishedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="percentage", type="decimal", precision=7, scale=2, nullable=true,
     *     options={"default": 1, "comment": "比例。default: 100%"})
     */
    private $percentage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean", options={"default": 0, "comment": "1: 失效; 0: 有效"})
     */
    private $isDeleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_hidden", type="boolean", options={"default":0, "comment": "0: 显示; 1: 隐藏"})
     */
    private $isHidden;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
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
        $this->setPercentage(1.00);
        $this->setStartedAt( new \Datetime() );
        $this->setFinishedAt( new \Datetime(date('Y-m-d', time()+ 86400) ));
    }
    /**
     * Set title
     *
     * @param string $title
     * @return AdActivity
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
     * Set description
     *
     * @param string $description
     * @return AdActivity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     * @return AdActivity
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set finishedAt
     *
     * @param \DateTime $finishedAt
     * @return AdActivity
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * Get finishedAt
     *
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * Set percentage
     *
     * @param float $percentage
     * @return AdActivity
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage
     *
     * @return float
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return AdActivity
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set isHidden
     *
     * @param boolean $isHidden
     * @return AdActivity
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     *
     * @return boolean
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AdActivity
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

    /**
     * to supress erro report
     * @return null
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }
}
