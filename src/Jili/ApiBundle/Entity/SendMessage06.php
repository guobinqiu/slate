<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SendMessage06
 *
 * @ORM\Table(name="send_message06")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\SendMessageRepository")
 */
class SendMessage06
{
    const SEND_FROM_SYS = 0;
    const READ_FLAG_UNREAD = 0;
    const DELETE_FLAG_CREATED =0;

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
     * @ORM\Column(name="sendFrom", type="integer", nullable=true)
     */
    private $sendFrom;

    /**
     * @var integer
     *
     * @ORM\Column(name="sendTo", type="integer", nullable=true)
     */
    private $sendTo;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createtime", type="datetime", nullable=true)
     */
    private $createtime;

    /**
     * @var integer
     *
     * @ORM\Column(name="read_flag", type="integer", nullable=true)
     */
    private $readFlag;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=true)
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
     * @return SendMessageXX
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
     * @return SendMessageXX
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
     * @return SendMessageXX
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
     * @param text $content
     * @return SendMessageXX
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createtime
     *
     * @param \DateTime $createtime
     * @return SendMessageXX
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
     * @return SendMessageXX
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
     * @return SendMessageXX
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

    public function __construct()
    {
        $this->setCreatetime( new \DateTime())
            ->setSendFrom(0)
            ->setReadFlag(0)
            ->setDeleteFlag(0);
    }
}
