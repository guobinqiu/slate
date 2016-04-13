<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SsiProjectParticipationHistory.
 *
 * @ORM\Table(name="ssi_project_participation_history", uniqueConstraints={@ORM\UniqueConstraint(name="completed_at_transaction_id_unique", columns={"completed_at", "transaction_id"})}, indexes={@ORM\Index(name="ssi_respondent_idx", columns={"ssi_respondent_id"}), @ORM\Index(name="ssi_respondent_created_at_idx", columns={"created_at"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class SsiProjectParticipationHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="ssi_respondent_id", type="integer", nullable=false)
     */
    private $ssiRespondentId;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="string", length=255, nullable=false)
     */
    private $transactionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set ssiRespondentId.
     *
     * @param int $ssiRespondentId
     *
     * @return SsiProjectParticipationHistory
     */
    public function setSsiRespondentId($ssiRespondentId)
    {
        $this->ssiRespondentId = $ssiRespondentId;

        return $this;
    }

    /**
     * Get ssiRespondentId.
     *
     * @return int
     */
    public function getSsiRespondentId()
    {
        return $this->ssiRespondentId;
    }

    /**
     * Set transactionId.
     *
     * @param string $transactionId
     *
     * @return SsiProjectParticipationHistory
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set completedAt.
     *
     * @param \DateTime $completedAt
     *
     * @return SsiProjectParticipationHistory
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt.
     *
     * @return \DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return SsiProjectParticipationHistory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return SsiProjectParticipationHistory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}
