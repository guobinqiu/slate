<?php
namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarketActivityClickNumber
 *
 * @ORM\Table(name="market_activity_click_number")
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\MarketActivityClickNumberRepository")
 */
class MarketActivityClickNumber
{
    /**
     * @var integer
     *
     * @ORM\Column(name="click_number", type="integer", nullable=false)
     */
    private $clickNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="market_activity_id", type="integer", nullable=false)
     */
    private $marketActivityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set clickNumber
     *
     * @param integer $clickNumber
     * @return MarketActivityClickNumber
     */
    public function setClickNumber($clickNumber)
    {
        $this->clickNumber = $clickNumber;

        return $this;
    }

    /**
     * Get clickNumber
     *
     * @return integer
     */
    public function getClickNumber()
    {
        return $this->clickNumber;
    }

    /**
     * Set marketActivityId
     *
     * @param integer $marketActivityId
     * @return MarketActivityClickNumber
     */
    public function setMarketActivityId($marketActivityId)
    {
        $this->marketActivityId = $marketActivityId;

        return $this;
    }

    /**
     * Get marketActivityId
     *
     * @return integer
     */
    public function getMarketActivityId()
    {
        return $this->marketActivityId;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
