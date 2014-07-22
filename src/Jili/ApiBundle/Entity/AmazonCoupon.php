<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AmazonCoupon
 *
 * @ORM\Table(name="amazon_coupon")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AmazonCouponRepository")
 */
class AmazonCoupon
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

     /**
     * @var string
     *
     * @ORM\Column(name="coupon_od", type="string" ,length=50)
     */
    private $couponOd;

     /**
     * @var string
     *
     * @ORM\Column(name="coupon_elec", type="string" ,length=50)
     */
    private $couponElec;


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
     * Set userid
     *
     * @param integer $userid
     * @return AmazonCoupon
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set couponOd
     *
     * @param string $couponOd
     * @return AmazonCoupon
     */
    public function setCouponOd($couponOd)
    {
        $this->couponOd = $couponOd;

        return $this;
    }

    /**
     * Get couponOd
     *
     * @return string
     */
    public function getCouponOd()
    {
        return $this->couponOd;
    }

    /**
     * Set couponElec
     *
     * @param string $couponElec
     * @return AmazonCoupon
     */
    public function setCouponElec($couponElec)
    {
        $this->couponElec = $couponElec;

        return $this;
    }

    /**
     * Get couponElec
     *
     * @return string
     */
    public function getCouponElec()
    {
        return $this->couponElec;
    }

}
