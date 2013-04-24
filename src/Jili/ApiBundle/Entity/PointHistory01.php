<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointHistory01
 *
 * @ORM\Table(name="point_history01")
 * @ORM\Entity
 */
class PointHistory01
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
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="point_change_num", type="string", length=45, nullable=true)
     */
    private $pointChangeNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="reason", type="integer", nullable=true)
     */
    private $reason;


}
