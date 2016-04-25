<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SsiProjectRespondent
 *
 * @ORM\Table(name="ssi_project_respondent",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="ssi_respondent_uniq", columns={"ssi_project_id", "ssi_respondent_id"})
 *     },
 *     indexes={
 *         @ORM\Index(name="ssi_project_mail_batch_idx", columns={"ssi_project_id", "ssi_mail_batch_id"}),
 *         @ORM\Index(name="ssi_respondent_idx", columns={"ssi_respondent_id"}),
 *         @ORM\Index(name="updated_at_answer_status_idx", columns={"updated_at", "answer_status"})
 *     }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 */
class SsiProjectRespondent
{
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
     * @ORM\Column(name="ssi_mail_batch_id", type="integer")
     */
    private $ssiMailBatchId;

    /**
     * @var string
     *
     * @ORM\Column(name="start_url_id", type="string", length=255)
     */
    private $startUrlId;

    /**
     * @var integer
     *
     * @ORM\Column(name="answer_status", type="smallint", options={"default": 1, "comment": "0:init, 2:reopened, 5:forwarded ,11:completed"})
     */
    private $answerStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="stash_data", type="text", nullable=true)
     */
    private $stashData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \Wenwen\AppBundle\Entity\SsiProject
     *
     * @ORM\ManyToOne(targetEntity="Wenwen\AppBundle\Entity\SsiProject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ssi_project_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $ssiProject;

    /**
     * @var \Wenwen\AppBundle\Entity\SsiRespondent
     *
     * @ORM\ManyToOne(targetEntity="Wenwen\AppBundle\Entity\SsiRespondent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ssi_respondent_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $ssiRespondent;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
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
     * Set ssiMailBatchId
     *
     * @param integer $ssiMailBatchId
     * @return SsiProjectRespondent
     */
    public function setSsiMailBatchId($ssiMailBatchId)
    {
        $this->ssiMailBatchId = $ssiMailBatchId;

        return $this;
    }

    /**
     * Get ssiMailBatchId
     *
     * @return integer
     */
    public function getSsiMailBatchId()
    {
        return $this->ssiMailBatchId;
    }

    /**
     * Set startUrlId
     *
     * @param string $startUrlId
     * @return SsiProjectRespondent
     */
    public function setStartUrlId($startUrlId)
    {
        $this->startUrlId = $startUrlId;

        return $this;
    }

    /**
     * Get startUrlId
     *
     * @return string
     */
    public function getStartUrlId()
    {
        return $this->startUrlId;
    }

    /**
     * Set answerStatus
     *
     * @param integer $answerStatus
     * @return SsiProjectRespondent
     */
    public function setAnswerStatus($answerStatus)
    {
        $this->answerStatus = $answerStatus;

        return $this;
    }

    /**
     * Get answerStatus
     *
     * @return integer
     */
    public function getAnswerStatus()
    {
        return $this->answerStatus;
    }

    /**
     * Set stashData
     *
     * @param string $stashData
     * @return SsiProjectRespondent
     */
    public function setStashData(array $stashData)
    {
        $this->stashData = json_encode($stashData);

        return $this;
    }

    /**
     * Get stashData
     *
     * @return string
     */
    public function getStashData()
    {
        return json_decode($this->stashData, true);
    }

    /**
     * Set completedAt
     *
     * @param \DateTime $completedAt
     * @return SsiProjectRespondent
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt
     *
     * @return \DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return SsiProjectRespondent
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SsiProjectRespondent
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
     * Set ssiProject
     *
     * @param \Wenwen\AppBundle\Entity\SsiProject $ssiProject
     * @return SsiProjectRespondent
     */
    public function setSsiProject(\Wenwen\AppBundle\Entity\SsiProject $ssiProject = null)
    {
        $this->ssiProject = $ssiProject;

        return $this;
    }

    /**
     * Get ssiProject
     *
     * @return \Wenwen\AppBundle\Entity\SsiProject
     */
    public function getSsiProject()
    {
        return $this->ssiProject;
    }

    /**
     * Set ssiRespondent
     *
     * @param \Wenwen\AppBundle\Entity\SsiRespondent $ssiRespondent
     * @return SsiProjectRespondent
     */
    public function setSsiRespondent(\Wenwen\AppBundle\Entity\SsiRespondent $ssiRespondent = null)
    {
        $this->ssiRespondent = $ssiRespondent;

        return $this;
    }

    /**
     * Get ssiRespondent
     *
     * @return \Wenwen\AppBundle\Entity\SsiRespondent
     */
    public function getSsiRespondent()
    {
        return $this->ssiRespondent;
    }

    /**
     * @ORM\PreUpdate
     */
    public function beforeUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}
