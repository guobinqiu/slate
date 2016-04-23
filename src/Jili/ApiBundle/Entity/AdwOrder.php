<?php
namespace Jili\ApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdwOrder
 *
 * @ORM\Table(name="adw_order")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdwOrderRepository")
 */
class AdwOrder
{
    /**
     *  @var const
     *  adw 成果类型 CPA,与实时接口的type一致,同时与ad_category ID_ADW_CPA 碰巧取值都是1
     */
    const INCENTIVE_TYPE_CPA=1;
    const ORDER_TYPE=2; // 合并后的order，关联表 cps_advertiserment

    /**
     *  @var const
     *  adw 成果类型 CPS
     */
    const INCENTIVE_TYPE_CPS=2;

    public function __construct()
    {
        $this->createTime = new \DateTime();
        //$this->adwReturnTime = new \DateTime();
        //$this->confirmTime = new \DateTime();
    }

     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
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
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer")
     */
    private $adid;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="happen_time", type="datetime", nullable=true)
     */
    private $happenTime;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="adw_return_time", type="datetime", nullable=true)
     */
    private $adwReturnTime;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirm_time", type="datetime", nullable=true)
     */
    private $confirmTime;


    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_type", type="integer", nullable=true)
     */
    private $incentiveType;


    /**
     * @var integer
     *
     * @ORM\Column(name="incentive", type="integer", nullable=true)
     */
    private $incentive;


    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_rate", type="integer", nullable=true)
     */
    private $incentiveRate;

    /**
     * @var float
     *
     * @ORM\Column(name="comm", type="float", nullable=true)
     */
    private $comm;

    /**
     * @var string
     *
     * @ORM\Column(name="ocd", type="string", length=100, nullable=true)
     */
    private $ocd;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_price", type="float", nullable=true)
     */
    private $orderPrice;


    /**
     * @var integer
     *
     * @ORM\Column(name="order_status", type="integer", length=2, nullable=false, options={"default": 0})
     */
    private $orderStatus;


    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", length=1, nullable=false, options={"default": 0})
     */
    private $deleteFlag;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_type", type="integer", nullable=true, options={"comment":"2:合并后的order"})
     */
    private $orderType;

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
     * @return AdwOrder
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
     * Set adid
     *
     * @param integer $adid
     * @return AdwOrder
     */
    public function setAdid($adid)
    {
        $this->adid = $adid;

        return $this;
    }

    /**
     * Get adid
     *
     * @return integer
     */
    public function getAdid()
    {
        return $this->adid;
    }


    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return AdwOrder
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }


    /**
     * Set happenTime
     *
     * @param \DateTime $happenTime
     * @return AdwOrder
     */
    public function setHappenTime($happenTime)
    {
        $this->happenTime = $happenTime;

        return $this;
    }

    /**
     * Get happenTime
     *
     * @return \DateTime
     */
    public function getHappenTime()
    {
        return $this->happenTime;
    }




    /**
     * Set adwReturnTime
     *
     * @param \DateTime $adwReturnTime
     * @return AdwOrder
     */
    public function setAdwReturnTime($adwReturnTime)
    {
        $this->adwReturnTime = $adwReturnTime;

        return $this;
    }

    /**
     * Get adwReturnTime
     *
     * @return \DateTime
     */
    public function getAdwReturnTime()
    {
        return $this->adwReturnTime;
    }



    /**
     * Set confirmTime
     *
     * @param \DateTime $confirmTime
     * @return AdwOrder
     */
    public function setConfirmTime($confirmTime)
    {
        $this->confirmTime = $confirmTime;

        return $this;
    }

    /**
     * Get confirmTime
     *
     * @return \DateTime
     */
    public function getConfirmTime()
    {
        return $this->confirmTime;
    }



    /**
     * Set incentiveType
     *
     * @param integer $incentiveType
     * @return AdwOrder
     */
    public function setIncentiveType($incentiveType)
    {
        $this->incentiveType = $incentiveType;

        return $this;
    }

    /**
     * Get incentiveType
     *
     * @return integer
     */
    public function getIncentiveType()
    {
        return $this->incentiveType;
    }

    /**
     * Set incentive
     *
     * @param integer $incentive
     * @return AdwOrder
     */
    public function setIncentive($incentive)
    {
        $this->incentive = $incentive;

        return $this;
    }

    /**
     * Get incentive
     *
     * @return integer
     */
    public function getIncentive()
    {
        return $this->incentive;
    }



    /**
     * Set incentiveRate
     *
     * @param integer $incentiveRate
     * @return AdwOrder
     */
    public function setIncentiveRate($incentiveRate)
    {
        $this->incentiveRate = $incentiveRate;

        return $this;
    }

    /**
     * Get comm
     *
     * @return float
     */
    public function getComm()
    {
        return $this->comm;
    }

    /**
     * Set comm
     *
     * @param float $comm
     * @return AdwOrder
     */
    public function setComm($comm)
    {
        $this->comm = $comm;

        return $this;
    }


    /**
     * Set ocd
     *
     * @param string $ocd
     * @return AdwOrder
     */
    public function setOcd($ocd)
    {
        $this->ocd = $ocd;

        return $this;
    }

    /**
     * Get ocd
     *
     * @return string
     */
    public function getOcd()
    {
        return $this->ocd;
    }



    /**
     * Get incentiveRate
     *
     * @return integer
     */
    public function getIncentiveRate()
    {
        return $this->incentiveRate;
    }




    /**
     * Set orderPrice
     *
     * @param integer $orderPrice
     * @return AdwOrder
     */
    public function setOrderPrice($orderPrice)
    {
        $this->orderPrice = $orderPrice;

        return $this;
    }

    /**
     * Get orderPrice
     *
     * @return integer
     */
    public function getOrderPrice()
    {
        return $this->orderPrice;
    }



    /**
     * Set orderStatus
     *
     * @param integer $orderStatus
     * @return AdwOrder
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get orderStatus
     *
     * @return integer
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }




    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return AdwOrder
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;

        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return integer
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
    }

    /**
     * Set orderType
     *
     * @param integer $orderType
     * @return AdwOrder
     */
    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;

        return $this;
    }

    /**
     * Get orderType
     *
     * @return integer
     */
    public function getOrderType()
    {
        return $this->orderType;
    }


}
