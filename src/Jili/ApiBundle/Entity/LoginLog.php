<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginLog
 *
 * @ORM\Table(name="login_log")
 * @ORM\Entity
 */
class LoginLog
{
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
     * @var \DateTime
     *
     * @ORM\Column(name="login_date", type="datetime", nullable=true)
     */
    private $loginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="login_ip", type="string", length=45, nullable=true)
     */
    private $loginIp;


}
