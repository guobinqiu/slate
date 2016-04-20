<?php

namespace Wenwen\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserWenwenLoginDeleted
 *
 * @ORM\Table(name="user_wenwen_login_deleted", uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class UserWenwenLoginDeleted
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

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
     * Set userId
     *
     * @param integer $userId
     * @return UserWenwenLoginDeleted
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
     * Set loginPasswordSalt
     *
     * @param string $loginPasswordSalt
     * @return UserWenwenLoginDeleted
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
     * @return UserWenwenLoginDeleted
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
     * @return UserWenwenLoginDeleted
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
}
