<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSignInDetail
 *
 * @ORM\Table(name="user_sign_in_detail")
 * @ORM\Entity
 */
class UserSignInDetail
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userSignInDetails")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sign_in_date", type="date")
     */
    private $signInDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sign_in_time", type="time")
     */
    private $signInTime;


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
     * @return UserSignInDetail
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
     * Set signInDate
     *
     * @param \DateTime $signInDate
     * @return UserSignInDetail
     */
    public function setSignInDate($signInDate)
    {
        $this->signInDate = $signInDate;

        return $this;
    }

    /**
     * Get signInDate
     *
     * @return \DateTime 
     */
    public function getSignInDate()
    {
        return $this->signInDate;
    }

    /**
     * Set signInTime
     *
     * @param \DateTime $signInTime
     * @return UserSignInDetail
     */
    public function setSignInTime($signInTime)
    {
        $this->signInTime = $signInTime;

        return $this;
    }

    /**
     * Get signInTime
     *
     * @return \DateTime 
     */
    public function getSignInTime()
    {
        return $this->signInTime;
    }
}
