<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WeiBoUser
 *
 * @ORM\Table(name="weibo_user",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="open_id_uniq", columns={"open_id"})
 *     },
 *     indexes={
 *         @ORM\Index(name="user_id_idx", columns={"user_id"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\WeiBoUserRepository")
 */
class WeiBoUser
{
    
    public function __construct()
    {
        $this->setRegistDate ( new \DateTime());
    }

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
     * @ORM\Column(name="user_id", type="integer", options={"comment":"jili用户id"})
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="open_id", type="string", nullable=true, options={"comment":"微博id唯一标识"})
     */
    private $openId;

     /**
     * @var datetime $registDate
     *
     * @ORM\Column(name="regist_date", type="datetime", nullable=true, options={"comment":"注册日期"})
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

    /**
     * Get registDate
     *
     * @return \DateTime 
     */
    public function getRegistDate()
    {
        return $this->registDate;
    }
}
