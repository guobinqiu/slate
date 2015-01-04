<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaoBaoUser
 *
 * @ORM\Table(name="taobao_user")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\TaoBaoUserRepository")
 */
class TaoBaoUser
{
    
    public function __construct()
    {
        $this->setRegistDate ( new \DateTime());
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="open_id", type="string",unique=true)
     */
    private $openId;

     /**
     * @var datetime $registDate
     *
     * @ORM\Column(name="regist_date", type="datetime", nullable=true)
     */
    private $registDate;
    
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
     * @param integer $userId
     * @return openId
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
     * Set openId
     *
     * @param String  $openId
     * @return openId
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;

        return $this;
    }

    /**
     * Get openId
     *
     * @return String
     */
    public function getOpenId()
    {
        return $this->openId;
    }
    
    /**
     * Set registDate
     *
     * @param \DateTime $registDate
     * @return User
     */
    public function setRegistDate($registDate)
    {
        $this->registDate = $registDate;

        return $this;
    }

    /**
     * Get registDate
     *
     * @return \DateTime
     */
    public function getRegistrDate()
    {
        return $this->registDate;
    }
}
