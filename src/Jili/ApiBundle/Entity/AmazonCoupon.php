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
     * @ORM\Column(name="coupon", type="string" ,length=50)
     */
    private $coupon;


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
     * Set coupon
     *
     * @param string $coupon
     * @return AmazonCoupon
     */
    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    
        return $this;
    }

    /**
     * Get coupon
     *
     * @return string 
     */
    public function getCoupon()
    {
        return $this->coupon;
    }
    
}
