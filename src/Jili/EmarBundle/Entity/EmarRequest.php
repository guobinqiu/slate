<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarRequest
 *
 * @ORM\Table(name="emar_request", uniqueConstraints={@ORM\UniqueConstraint(name="tag", columns={"tag"})})
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarRequestRepository")
 */
class EmarRequest
{
    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=16, nullable=false)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;

    /**
     * @var integer
     *
     * @ORM\Column(name="size_up", type="integer", nullable=false)
     */
    private $sizeUp;

    /**
     * @var integer
     *
     * @ORM\Column(name="size_down", type="integer", nullable=false)
     */
    private $sizeDown;

    /**
     * @var string
     *
     * @ORM\Column(name="time_consumed_total", type="decimal", precision=10, scale=4, nullable=false)
     */
    private $timeConsumedTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set tag
     *
     * @param string $tag
     * @return EmarRequest
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return EmarRequest
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set sizeUp
     *
     * @param integer $sizeUp
     * @return EmarRequest
     */
    public function setSizeUp($sizeUp)
    {
        $this->sizeUp = $sizeUp;

        return $this;
    }

    /**
     * Get sizeUp
     *
     * @return integer
     */
    public function getSizeUp()
    {
        return $this->sizeUp;
    }

    /**
     * Set sizeDown
     *
     * @param integer $sizeDown
     * @return EmarRequest
     */
    public function setSizeDown($sizeDown)
    {
        $this->sizeDown = $sizeDown;

        return $this;
    }

    /**
     * Get sizeDown
     *
     * @return integer
     */
    public function getSizeDown()
    {
        return $this->sizeDown;
    }

    /**
     * Set timeConsumedTotal
     *
     * @param string $timeConsumedTotal
     * @return EmarRequest
     */
    public function setTimeConsumedTotal($timeConsumedTotal)
    {
        $this->timeConsumedTotal = $timeConsumedTotal;

        return $this;
    }

    /**
     * Get timeConsumedTotal
     *
     * @return string
     */
    public function getTimeConsumedTotal()
    {
        return $this->timeConsumedTotal;
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
