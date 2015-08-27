<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoteAnswerYyyymm
 *
 * @ORM\Table(name="vote_answer_yyyymm", uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id", "vote_id"})}, indexes={@ORM\Index(name="vote_id", columns={"vote_id"})})
 * @ORM\Entity
 */
class VoteAnswerYyyymm
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
     * Set userId
     *
     * @param integer $userId
     * @return VoteAnswerYyyymm
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
     * Set voteId
     *
     * @param integer $voteId
     * @return VoteAnswerYyyymm
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
     * @return VoteAnswerYyyymm
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return VoteAnswerYyyymm
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
     * @return VoteAnswerYyyymm
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
