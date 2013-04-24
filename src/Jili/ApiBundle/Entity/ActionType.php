<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActionType
 *
 * @ORM\Table(name="action_type")
 * @ORM\Entity
 */
class ActionType
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
     * @ORM\Column(name="action_type", type="string", length=45, nullable=true)
     */
    private $actionType;


}
