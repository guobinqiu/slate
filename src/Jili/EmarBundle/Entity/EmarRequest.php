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
     * @ORM\Column(name="tag", type="string", length=16, nullable=false, options={"comment":"时间标签，YmdHi"})
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer", nullable=false, options={"default":0, "comment":"对emar api请求的次数"})
     */
    private $count;

    /**
     * @var integer
     *
     * @ORM\Column(name="size_up", type="integer", nullable=false, options={"default":0, "comment":"对emar api请求 的size之和"})
     */
    private $sizeUp;

    /**
     * @var integer
     *
     * @ORM\Column(name="size_down", type="integer", nullable=false, options={"default":0, "comment":"对emar api返回的size之和"})
     */
    private $sizeDown;

    /**
     * @var string
     *
     * @ORM\Column(name="time_consumed_total", type="decimal", precision=10, scale=4, nullable=false, options={"default":"0", "comment":"使用时间之和"})
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
