<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoteChoice
 *
 * @ORM\Table(name="vote_choice", uniqueConstraints={@ORM\UniqueConstraint(name="vote_choice_uk", columns={"vote_id", "answer_number"})}, indexes={@ORM\Index(name="IDX_CD4C28C872DCDAFC", columns={"vote_id"})})
 * @ORM\Entity
 */
class VoteChoice
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="answer_number", type="boolean", nullable=false)
     */
    private $answerNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
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

    /**
     * @var \Jili\ApiBundle\Entity\Vote
     *
     * @ORM\ManyToOne(targetEntity="Jili\ApiBundle\Entity\Vote")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="vote_id", referencedColumnName="id")
     * })
     */
    protected $vote;

    /**
     * @var integer
     *
     * @ORM\Column(name="vote_id", type="integer")
     */
    private $voteId;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Set answerNumber
     *
     * @param boolean $answerNumber
     * @return VoteChoice
     */
    public function setAnswerNumber($answerNumber)
    {
        $this->answerNumber = $answerNumber;

        return $this;
    }

    /**
     * Get answerNumber
     *
     * @return boolean
     */
    public function getAnswerNumber()
    {
        return $this->answerNumber;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return VoteChoice
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return VoteChoice
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
     * @return VoteChoice
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
     * Set vote
     *
     * @param \Jili\ApiBundle\Entity\Vote $vote
     * @return VoteChoice
     */
    public function setVote(\Jili\ApiBundle\Entity\Vote $vote = null)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return \Jili\ApiBundle\Entity\Vote
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set voteId
     *
     * @param integer $voteId
     * @return VoteChoice
     */
    public function setVoteId($voteId)
    {
        $this->voteId = $voteId;

        return $this;
    }

    /**
     * Get voteId
     *
     * @return integer
     */
    public function getVoteId()
    {
        return $this->voteId;
    }

}
