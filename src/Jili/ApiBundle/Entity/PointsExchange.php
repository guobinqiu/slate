<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointsExchange
 *
 * @ORM\Table(name="points_exchange")
 * @ORM\Entity
 */
class PointsExchange
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
     * @ORM\Column(name="exchange_date", type="datetime", nullable=true)
     */
    private $exchangeDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="account", type="string", length=45, nullable=true)
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="point", type="string", length=45, nullable=true)
     */
    private $point;

    /**
     * @var string
     *
     * @ORM\Column(name="exchanged_point", type="string", length=45, nullable=true)
     */
    private $exchangedPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
     */
    private $ip;


}
