<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QQUser
 *
 * @ORM\Table(name="qq_user",
 *     indexes={
 *         @ORM\Index(name="userid_index", columns={"user_id"}),
 *         @ORM\Index(name="openid_index", columns={"open_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\QQUserRepository")
 */
class QQUser
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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="open_id", type="string", nullable=true)
     */
    private $openId;



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
}
