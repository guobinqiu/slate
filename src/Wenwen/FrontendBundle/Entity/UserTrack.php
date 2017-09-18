<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wenwen\FrontendBundle\Model\OwnerType;

/**
 * UserTrack
 *
 * @ORM\Table(name="user_track")
 * @ORM\Entity
 */
class UserTrack
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="userTrack")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * a 32-bit integer representing the browsers fingerprint
     *
     * @var string
     *
     * @ORM\Column(name="current_fingerprint", type="string", length=10, nullable=true)
     */
    private $currentFingerprint;

    /**
     * a 32-bit integer representing the previous browsers fingerprint
     *
     * @var string
     *
     * @ORM\Column(name="last_fingerprint", type="string", length=10, nullable=true)
     */
    private $lastFingerprint;

    /**
     * Increased every time a sign in is made (by form, openid, oauth)
     *
     * @var integer
     *
     * @ORM\Column(name="sign_in_count", type="integer")
     */
    private $signInCount;

    /**
     * A timestamp updated when the user signs in
     *
     * @var \DateTime
     *
     * @ORM\Column(name="current_sign_in_at", type="datetime")
     */
    private $currentSignInAt;

    /**
     * Holds the timestamp of the previous sign in
     *
     * @var \DateTime
     *
     * @ORM\Column(name="last_sign_in_at", type="datetime", nullable=true)
     */
    private $lastSignInAt;

    /**
     * The remote ip updated when the user sign in
     *
     * @var string
     *
     * @ORM\Column(name="current_sign_in_ip", type="string", length=15, nullable=true)
     */
    private $currentSignInIp;

    /**
     * Holds the remote ip of the previous sign in
     *
     * @var string
     *
     * @ORM\Column(name="last_sign_in_ip", type="string", length=15, nullable=true)
     */
    private $lastSignInIp;

    /**
     * @var string
     *
     * @ORM\Column(name="register_route", type="string", length=24, nullable=true)
     */
    private $registerRoute;

    /**
     * @var string
     *
     * @ORM\Column(name="owner_type", type="string", length=24, nullable=false, options={"default": "dataspring"})
     */
    private $ownerType;

    public function __construct()
    {
        $this->signInCount = 0;
        $this->currentSignInAt = new \DateTime();
        $this->currentSignInIp = '000.000.000.000';
        $this->ownerType = OwnerType::DATASPRING;
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
     * Set user
     *
     * @param User $user
     * @return UserTrack
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set currentFingerprint
     *
     * @param string $currentFingerprint
     * @return UserTrack
     */
    public function setCurrentFingerprint($currentFingerprint)
    {
        $this->currentFingerprint = $currentFingerprint;

        return $this;
    }

    /**
     * Get currentFingerprint
     *
     * @return string
     */
    public function getCurrentFingerprint()
    {
        return $this->currentFingerprint;
    }

    /**
     * Set lastFingerprint
     *
     * @param string $lastFingerprint
     * @return UserTrack
     */
    public function setLastFingerprint($lastFingerprint)
    {
        $this->lastFingerprint = $lastFingerprint;

        return $this;
    }

    /**
     * Get lastFingerprint
     *
     * @return string
     */
    public function getLastFingerprint()
    {
        return $this->lastFingerprint;
    }

    /**
     * Set signInCount
     *
     * @param integer $signInCount
     * @return UserTrack
     */
    public function setSignInCount($signInCount)
    {
        $this->signInCount = $signInCount;

        return $this;
    }

    /**
     * Get signInCount
     *
     * @return integer
     */
    public function getSignInCount()
    {
        return $this->signInCount;
    }

    /**
     * Set currentSignInAt
     *
     * @param \DateTime $currentSignInAt
     * @return UserTrack
     */
    public function setCurrentSignInAt($currentSignInAt)
    {
        $this->currentSignInAt = $currentSignInAt;

        return $this;
    }

    /**
     * Get currentSignInAt
     *
     * @return \DateTime
     */
    public function getCurrentSignInAt()
    {
        return $this->currentSignInAt;
    }

    /**
     * Set lastSignInAt
     *
     * @param \DateTime $lastSignInAt
     * @return UserTrack
     */
    public function setLastSignInAt($lastSignInAt)
    {
        $this->lastSignInAt = $lastSignInAt;

        return $this;
    }

    /**
     * Get lastSignInAt
     *
     * @return \DateTime
     */
    public function getLastSignInAt()
    {
        return $this->lastSignInAt;
    }

    /**
     * Set currentSignInIp
     *
     * @param string $currentSignInIp
     * @return UserTrack
     */
    public function setCurrentSignInIp($currentSignInIp)
    {
        $this->currentSignInIp = $currentSignInIp;

        return $this;
    }

    /**
     * Get currentSignInIp
     *
     * @return string
     */
    public function getCurrentSignInIp()
    {
        return $this->currentSignInIp;
    }

    /**
     * Set lastSignInIp
     *
     * @param string $lastSignInIp
     * @return UserTrack
     */
    public function setLastSignInIp($lastSignInIp)
    {
        $this->lastSignInIp = $lastSignInIp;

        return $this;
    }

    /**
     * Get lastSignInIp
     *
     * @return string
     */
    public function getLastSignInIp()
    {
        return $this->lastSignInIp;
    }

    /**
     * Set registerRoute
     *
     * @param string $registerRoute
     * @return UserTrack
     */
    public function setRegisterRoute($registerRoute)
    {
        $this->registerRoute = $registerRoute;

        return $this;
    }

    /**
     * Get registerRoute
     *
     * @return string
     */
    public function getRegisterRoute()
    {
        return $this->registerRoute;
    }

    /**
     * Set ownerType
     *
     * @param string $ownerType
     * @return UserTrack
     */
    public function setOwnerType($ownerType)
    {
        if (null === $ownerType) {
            $ownerType = OwnerType::DATASPRING;
        }
        $this->ownerType = $ownerType;

        return $this;
    }

    /**
     * Get ownerType
     *
     * @return string
     */
    public function getOwnerType()
    {
        return $this->ownerType;
    }
}
