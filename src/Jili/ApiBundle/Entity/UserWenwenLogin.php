<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserWenwenLogin
 *
 * @ORM\Table(name="user_wenwen_login", uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class UserWenwenLogin
{
    /**
     * @var string
     *
     * @ORM\Column(name="login_password_salt", type="text", nullable=true)
     */
    private $loginPasswordSalt;

    /**
     * @var string
     *
     * @ORM\Column(name="login_password_crypt_type", type="string", length=50, nullable=true)
     */
    private $loginPasswordCryptType;

    /**
     * @var string
     *
     * @ORM\Column(name="login_password", type="text", nullable=true)
     */
    private $loginPassword;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Jili\ApiBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Jili\ApiBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set loginPasswordSalt
     *
     * @param string $loginPasswordSalt
     * @return UserWenwenLogin
     */
    public function setLoginPasswordSalt($loginPasswordSalt)
    {
        $this->loginPasswordSalt = $loginPasswordSalt;

        return $this;
    }

    /**
     * Get loginPasswordSalt
     *
     * @return string 
     */
    public function getLoginPasswordSalt()
    {
        return $this->loginPasswordSalt;
    }

    /**
     * Set loginPasswordCryptType
     *
     * @param string $loginPasswordCryptType
     * @return UserWenwenLogin
     */
    public function setLoginPasswordCryptType($loginPasswordCryptType)
    {
        $this->loginPasswordCryptType = $loginPasswordCryptType;

        return $this;
    }

    /**
     * Get loginPasswordCryptType
     *
     * @return string 
     */
    public function getLoginPasswordCryptType()
    {
        return $this->loginPasswordCryptType;
    }

    /**
     * Set loginPassword
     *
     * @param string $loginPassword
     * @return UserWenwenLogin
     */
    public function setLoginPassword($loginPassword)
    {
        $this->loginPassword = $loginPassword;

        return $this;
    }

    /**
     * Get loginPassword
     *
     * @return string 
     */
    public function getLoginPassword()
    {
        return $this->loginPassword;
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
     * @param \Jili\ApiBundle\Entity\User $user
     * @return UserWenwenLogin
     */
    public function setUser(\Jili\ApiBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Jili\ApiBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
