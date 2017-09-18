<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user_device",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uniq_device", columns={"udid", "type"})
 *     }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UserDevice
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="userDevice")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="udid", type="string", nullable=false)
     * @Assert\NotBlank
     */
    private $udid;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", nullable=false, length=50)
     * @Assert\NotBlank
     * @Assert\Choice({"ios", "android", "browser"})
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="os_version", type="string", nullable=true)
     */
    private $osVersion;

    /**
     * @var boolean
     * @ORM\Column(name="is_notifiable", type="boolean", nullable=false, options={"default":true})
     */
    private $isNotifiable;

    /**
     * @var \DateTime
     * @ORM\Column(name="expired_at", type="datetime", nullable=false)
     */
    private $expiredAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUdid($udid)
    {
        $this->udid = $udid;

        return $this;
    }

    public function getUdid()
    {
        return $this->udid;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setOsVersion($osVersion)
    {
        $this->osVersion = $osVersion;

        return $this;
    }

    public function getOsVersion()
    {
        return $this->osVersion;
    }

    public function setNotifiable($isNotifiable)
    {
        $this->isNotifiable = $isNotifiable;

        return $this;
    }

    public function isNotifiable()
    {
        return $this->isNotifiable;
    }

    public function isExpired()
    {
        return new \DateTime() > $this->expiredAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        $date = new \DateTime();
        $date->modify('+1 year');
        $this->expiredAt = $date;
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}