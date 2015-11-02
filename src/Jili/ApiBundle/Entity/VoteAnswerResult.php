<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoteAnswerResult
 *
 * @ORM\Table(name="vote_answer_result", uniqueConstraints={@ORM\UniqueConstraint(name="vote_id", columns={"vote_id", "answer_number"})})
 * @ORM\Entity
 */
class VoteAnswerResult
{
    /**
     * @var integer
     *
     * @ORM\Column(name="vote_id", type="integer", nullable=false)
     */
    private $voteId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="answer_number", type="boolean", nullable=false)
     */
    private $answerNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="answer_count", type="integer", nullable=true)
     */
    private $answerCount;

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
     * Set voteId
     *
     * @param integer $voteId
     * @return VoteAnswerResult
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

    /**
     * Set answerNumber
     *
     * @param boolean $answerNumber
     * @return VoteAnswerResult
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
     * Set answerCount
     *
     * @param integer $answerCount
     * @return VoteAnswerResult
     */
    public function setAnswerCount($answerCount)
    {
        $this->answerCount = $answerCount;
    
        return $this;
    }

    /**
     * Get answerCount
     *
     * @return integer 
     */
    public function getAnswerCount()
    {
        return $this->answerCount;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return VoteAnswerResult
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
     * @return VoteAnswerResult
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
