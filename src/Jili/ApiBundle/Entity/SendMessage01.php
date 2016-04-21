<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SendMessage01
 *
 * @ORM\Table(name="send_message01")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\SendMessageRepository")
 */
class SendMessage01 //extends SendMessageBase
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
     */
    private $sendFrom;

    /**
     * @var integer
     */
    private $sendTo;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $createtime;

    /**
     * @var integer
     */
    private $readFlag;

    /**
     * @var integer
     */
    private $deleteFlag;


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
     * Set sendFrom
     *
     * @param integer $sendFrom
     * @return SendMessage01
     */
    public function setSendFrom($sendFrom)
    {
        $this->sendFrom = $sendFrom;

        return $this;
    }

    /**
     * Get sendFrom
     *
     * @return integer 
     */
    public function getSendFrom()
    {
        return $this->sendFrom;
    }

    /**
     * Set sendTo
     *
     * @param integer $sendTo
     * @return SendMessage01
     */
    public function setSendTo($sendTo)
    {
        $this->sendTo = $sendTo;

        return $this;
    }

    /**
     * Get sendTo
     *
     * @return integer 
     */
    public function getSendTo()
    {
        return $this->sendTo;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SendMessage01
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return SendMessage01
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createtime
     *
     * @param \DateTime $createtime
     * @return SendMessage01
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

    /**
     * Set readFlag
     *
     * @param integer $readFlag
     * @return SendMessage01
     */
    public function setReadFlag($readFlag)
    {
        $this->readFlag = $readFlag;

        return $this;
    }

    /**
     * Get readFlag
     *
     * @return integer 
     */
    public function getReadFlag()
    {
        return $this->readFlag;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return SendMessage01
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;

        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return integer 
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
    }
}
