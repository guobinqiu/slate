<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 奖池
 *
 * @ORM\Table(name="prize_items")
 * @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\PrizeItemRepository")
 */
class PrizeItem
{
    const TYPE_BIG = '大奖池';
    const TYPE_SMALL = '小奖池';
    const FIRST_PRIZE_POINTS = 300000;
    const POINT_BALANCE_BASE = 89991;
    const POINT_BALANCE_MIN = 0;
    const POINT_BALANCE_MAX = 99999999;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer")
     */
    private $points;

    /**
     * @var string
     *
     * @ORM\Column(name="percent", type="string", length=10, nullable=true)
     */
    private $percent;

    /**
     * @var integer
     *
     * @ORM\Column(name="min", type="integer")
     */
    private $min;

    /**
     * @var integer
     *
     * @ORM\Column(name="max", type="integer")
     */
    private $max;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

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
     * Set points
     *
     * @param integer $points
     * @return PrizeItem
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set percent
     *
     * @param string $percent
     * @return PrizeItem
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return string 
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set min
     *
     * @param integer $min
     * @return PrizeItem
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return integer 
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param integer $max
     * @return PrizeItem
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return integer 
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return PrizeItem
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

    /**
     * Set quantity
     *
     * @param integer $max
     * @return PrizeItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get $quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
