<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointsExchangeType
 *
 * @ORM\Table(name="points_exchange_type")
 * @ORM\Entity
 */
class PointsExchangeType
{
    const TYPE_91WENWEN = 1;
    const TYPE_AMAZON = 2;
    const TYPE_ALIPAY = 3;
    const TYPE_MOBILE = 4;
    const TYPE_FLOW = 5;

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
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;



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
     * Set type
     *
     * @param string $type
     * @return PointsExchangeType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
