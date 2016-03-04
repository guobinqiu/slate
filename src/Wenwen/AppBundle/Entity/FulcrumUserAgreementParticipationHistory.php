<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FulcrumUserAgreementParticipationHistory
 *
 * @ORM\Table(name="fulcrum_user_agreement_participation_history", uniqueConstraints={@ORM\UniqueConstraint(name="app_member_id_uniq_key", columns={"app_member_id"})})
 * @ORM\Entity
 */
class FulcrumUserAgreementParticipationHistory
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
     * @var string
     *
     * @ORM\Column(name="app_member_id", type="string", length=255, nullable=false)
     */
    private $appMemberId;

    /**
     * @var integer
     *
     * @ORM\Column(name="agreement_status", type="integer", nullable=false)
     */
    private $agreementStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="stash_data", type="text", nullable=true)
     */
    private $stashData;

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

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setAgreementStatus(0);
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
     * Set appMemberId
     *
     * @param string $appMemberId
     * @return FulcrumUserAgreementParticipationHistory
     */
    public function setAppMemberId($appMemberId)
    {
        $this->appMemberId = $appMemberId;

        return $this;
    }

    /**
     * Get appMemberId
     *
     * @return string 
     */
    public function getAppMemberId()
    {
        return $this->appMemberId;
    }

    /**
     * Set agreementStatus
     *
     * @param integer $agreementStatus
     * @return FulcrumUserAgreementParticipationHistory
     */
    public function setAgreementStatus($agreementStatus)
    {
        $this->agreementStatus = $agreementStatus;

        return $this;
    }

    /**
     * Get agreementStatus
     *
     * @return integer 
     */
    public function getAgreementStatus()
    {
        return $this->agreementStatus;
    }

    /**
     * Set stashData
     *
     * @param string $stashData
     * @return FulcrumUserAgreementParticipationHistory
     */
    public function setStashData($stashData)
    {
        $this->stashData = $stashData;

        return $this;
    }

    /**
     * Get stashData
     *
     * @return string 
     */
    public function getStashData()
    {
        return $this->stashData;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return FulcrumUserAgreementParticipationHistory
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
     * @return FulcrumUserAgreementParticipationHistory
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

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }
}
