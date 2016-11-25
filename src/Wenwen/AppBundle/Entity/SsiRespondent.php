<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SsiRespondent
 *
 * @ORM\Table(name="ssi_respondent", uniqueConstraints={@ORM\UniqueConstraint(name="partner_app_member_id_UNIQUE", columns={"user_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Wenwen\AppBundle\Repository\SsiRespondentRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class SsiRespondent
{
    const STATUS_PERMISSION_NO = 0;
    const STATUS_PERMISSION_YES = 1;
    const STATUS_PRESCREENED = 10;
    const STATUS_ACTIVE = 10;

    public static $base_url = 'http://tracking.surveycheck.com/aff_c?offer_id=3135&aff_id=1346&aff_sub5=wwcn-%d';

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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var \Wenwen\FrontendBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Wenwen\FrontendBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_flag", type="smallint", nullable=true,
     *     options={"default": 1, "comment": "0:permission_no,1:permission_yes, 10:active"})
     */
    private $statusFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="stash_data", type="text", nullable=true)
     */
    private $stashData;

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
     * Set userId
     *
     * @param  integer       $userId
     * @return SsiRespondent
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
     * Set user
     */
    public function setUser(\Wenwen\FrontendBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Wenwen\FrontendBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set statusFlag
     *
     * @param  integer       $statusFlag
     * @return SsiRespondent
     */
    public function setStatusFlag($statusFlag)
    {
        $this->statusFlag = $statusFlag;

        return $this;
    }

    /**
     * Get statusFlag
     *
     * @return integer
     */
    public function getStatusFlag()
    {
        return $this->statusFlag;
    }

    /**
     * Set stashData
     *
     * @param  string        $stashData
     * @return SsiRespondent
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
     * @param  \DateTime     $updatedAt
     * @return SsiRespondent
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
     * @param  \DateTime     $createdAt
     * @return SsiRespondent
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

    public function isActive()
    {
        return $this->getStatusFlag() == self::STATUS_ACTIVE;
    }

    public function needPrescreening()
    {
        return $this->getStatusFlag() == self::STATUS_PERMISSION_YES;
    }

    public function getPrescreeningSurveyUrl()
    {
        return sprintf(self::$base_url,  $this->getId());
    }

    /**
     * @param $respondentId shared with SSI. format: wwcn-12345
     */
    public static function parseRespondentId($respondentId)
    {
        if (preg_match('/\Awwcn-(\d+)\z/', $respondentId, $matches)) {
            return $matches[1];
        }

        return;
    }

    /**
     * @ORM\PreUpdate
     */
    public function beforeUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}