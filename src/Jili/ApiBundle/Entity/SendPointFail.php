<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SendPointFail
 *
 * @ORM\Table(name="send_point_fail")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\SendPointFailRepository")
 */
class SendPointFail
{
    public function __construct()
    {
        $this->createtime = new \DateTime();
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_type", type="integer")
     */
    private $sendType;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createtime;

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
     * @return SendPointFail
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
     * Set sendType
     *
     * @param integer $sendType
     * @return SendPointFail
     */
    public function setSendType($sendType)
    {
        $this->sendType = $sendType;

        return $this;
    }

    /**
     * Get sendType
     *
     * @return integer
     */
    public function getSendType()
    {
        return $this->sendType;
    }

     /**
     * Set createtime
     *
     * @param \DateTime $createtime
     * @return SendPointFail
     */
    public function setCreatetime($createtime)
    {
        $this->createtime = $createtime;

        return $this;
    }

    /**
     * Get createtime
     *
     * @return \DateTime
     */
    public function getCreatetime()
    {
        return $this->createtime;
    }


}
