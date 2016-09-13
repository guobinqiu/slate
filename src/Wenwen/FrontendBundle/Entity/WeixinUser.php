<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="weixin_user")
 * @ORM\Entity
 */
class WeixinUser
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
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="open_id", type="string", nullable=false)
     */
    private $openId;

    /**
     * @ORM\Column(name="nickname", type="string")
     */
    private $nickname;

    /**
     * @ORM\Column(name="photo", type="string")
     */
    private $photo;

    /**
     * @ORM\Column(name="gender", type="string")
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="union_id", type="string", nullable=true)
     */
    private $unionId;

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

    public function setOpenId($openId)
    {
        $this->openId = $openId;

        return $this;
    }

    public function getOpenId()
    {
        return $this->openId;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setUnionId($unionId)
    {
        $this->unionId = $unionId;

        return $this;
    }

    public function getUnionId()
    {
        return $this->unionId;
    }
}