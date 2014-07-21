<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PagOrder
 *
 * @ORM\Table(name="pag_order")
 * @ORM\Entity()
 */
class PagOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=50)
     */
    private $sessionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="point_uid", type="integer")
     */
    private $pointUid;

    /**
     * @var string
     *
     * @ORM\Column(name="point_pid", type="string", length=50)
     */
    private $pointPid;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=20)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="date2", type="string", length=20)
     */
    private $date2;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="amounts", type="float")
     */
    private $amounts;

    /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer")
     */
    private $point;


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
     * Set sessionId
     *
     * @param string $sessionId
     * @return PagOrder
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }


    /**
     * Set pointUid
     *
     * @param integer $pointUid
     * @return PagOrder
     */
    public function setPointUid($pointUid)
    {
        $this->pointUid = $pointUid;

        return $this;
    }

    /**
     * Get pointUid
     *
     * @return integer
     */
    public function getPointUid()
    {
        return $this->pointUid;
    }


    /**
     * Set pointPid
     *
     * @param string $pointPid
     * @return PagOrder
     */
    public function setPointPid($pointPid)
    {
        $this->pointPid = $pointPid;

        return $this;
    }

    /**
     * Get pointPid
     *
     * @return string
     */
    public function getPointPid()
    {
        return $this->pointPid;
    }

    /**
     * Set date
     *
     * @param string $date
     * @return PagOrder
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * Set date2
     *
     * @param string $date2
     * @return PagOrder
     */
    public function setDate2($date2)
    {
        $this->date2 = $date2;

        return $this;
    }

    /**
     * Get date2
     *
     * @return string
     */
    public function getDate2()
    {
        return $this->date2;
    }


    /**
     * Set price
     *
     * @param float $price
     * @return PagOrder
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }


    /**
     * Set status
     *
     * @param integer $status
     * @return PagOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Set amounts
     *
     * @param float $amounts
     * @return PagOrder
     */
    public function setAmounts($amounts)
    {
        $this->amounts = $amounts;

        return $this;
    }

    /**
     * Get amounts
     *
     * @return float
     */
    public function getAmounts()
    {
        return $this->amounts;
    }


    /**
     * Set point
     *
     * @param integer $point
     * @return PagOrder
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer
     */
    public function getPoint()
    {
        return $this->point;
    }




}
