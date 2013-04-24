<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointReason
 *
 * @ORM\Table(name="point_reason")
 * @ORM\Entity
 */
class PointReason
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
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=45, nullable=true)
     */
    private $reason;


}
