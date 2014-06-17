<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarActivityCommission
 *
 * @ORM\Table(name="emar_activity_commission", indexes={@ORM\Index(name="mall_name", columns={"mall_name"})})
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarActivityCommissionRepository")
 */
class EmarActivityCommission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="activity_id", type="integer", nullable=false)
     */
    private $activityId;

    /**
     * @var string
     *
     * @ORM\Column(name="activity_name", type="string", length=100, nullable=true)
     */
    private $activityName;

    /**
     * @var string
     *
     * @ORM\Column(name="activity_category", type="string", length=100, nullable=true)
     */
    private $activityCategory;

    /**
     * @var boolean
     *
     * @ORM\Column(name="commission_id", type="boolean", nullable=false)
     */
    private $commissionId;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_number", type="string", length=100, nullable=true)
     */
    private $commissionNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_name", type="string", length=200, nullable=true)
     */
    private $commissionName;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="string", length=100, nullable=true)
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_period", type="string", length=100, nullable=true)
     */
    private $commissionPeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="apply_products", type="string", length=200, nullable=true)
     */
    private $applyProducts;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="mall_name", type="string", length=100, nullable=true)
     */
    private $mallName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="rebate_type", type="boolean", nullable=true)
     */
    private $rebateType;

    /**
     * @var string
     *
     * @ORM\Column(name="rebate", type="string", length=10, nullable=true)
     */
    private $rebate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set activityId
     *
     * @param integer $activityId
     * @return EmarActivityCommission
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;

        return $this;
    }

    /**
     * Get activityId
     *
     * @return integer
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * Set activityName
     *
     * @param string $activityName
     * @return EmarActivityCommission
     */
    public function setActivityName($activityName)
    {
        $this->activityName = $activityName;

        return $this;
    }

    /**
     * Get activityName
     *
     * @return string
     */
    public function getActivityName()
    {
        return $this->activityName;
    }

    /**
     * Set activityCategory
     *
     * @param string $activityCategory
     * @return EmarActivityCommission
     */
    public function setActivityCategory($activityCategory)
    {
        $this->activityCategory = $activityCategory;

        return $this;
    }

    /**
     * Get activityCategory
     *
     * @return string
     */
    public function getActivityCategory()
    {
        return $this->activityCategory;
    }

    /**
     * Set commissionId
     *
     * @param boolean $commissionId
     * @return EmarActivityCommission
     */
    public function setCommissionId($commissionId)
    {
        $this->commissionId = $commissionId;

        return $this;
    }

    /**
     * Get commissionId
     *
     * @return boolean
     */
    public function getCommissionId()
    {
        return $this->commissionId;
    }

    /**
     * Set commissionNumber
     *
     * @param string $commissionNumber
     * @return EmarActivityCommission
     */
    public function setCommissionNumber($commissionNumber)
    {
        $this->commissionNumber = $commissionNumber;

        return $this;
    }

    /**
     * Get commissionNumber
     *
     * @return string
     */
    public function getCommissionNumber()
    {
        return $this->commissionNumber;
    }

    /**
     * Set commissionName
     *
     * @param string $commissionName
     * @return EmarActivityCommission
     */
    public function setCommissionName($commissionName)
    {
        $this->commissionName = $commissionName;

        return $this;
    }

    /**
     * Get commissionName
     *
     * @return string
     */
    public function getCommissionName()
    {
        return $this->commissionName;
    }

    /**
     * Set commission
     *
     * @param string $commission
     * @return EmarActivityCommission
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set commissionPeriod
     *
     * @param string $commissionPeriod
     * @return EmarActivityCommission
     */
    public function setCommissionPeriod($commissionPeriod)
    {
        $this->commissionPeriod = $commissionPeriod;

        return $this;
    }

    /**
     * Get commissionPeriod
     *
     * @return string
     */
    public function getCommissionPeriod()
    {
        return $this->commissionPeriod;
    }

    /**
     * Set applyProducts
     *
     * @param string $applyProducts
     * @return EmarActivityCommission
     */
    public function setApplyProducts($applyProducts)
    {
        $this->applyProducts = $applyProducts;

        return $this;
    }

    /**
     * Get applyProducts
     *
     * @return string
     */
    public function getApplyProducts()
    {
        return $this->applyProducts;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return EmarActivityCommission
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set mallName
     *
     * @param string $mallName
     * @return EmarActivityCommission
     */
    public function setMallName($mallName)
    {
        $this->mallName = $mallName;

        return $this;
    }

    /**
     * Get mallName
     *
     * @return string
     */
    public function getMallName()
    {
        return $this->mallName;
    }

    /**
     * Set rebateType
     *
     * @param boolean $rebateType
     * @return EmarActivityCommission
     */
    public function setRebateType($rebateType)
    {
        $this->rebateType = $rebateType;

        return $this;
    }

    /**
     * Get rebateType
     *
     * @return boolean
     */
    public function getRebateType()
    {
        return $this->rebateType;
    }

    /**
     * Set rebate
     *
     * @param string $rebate
     * @return EmarActivityCommission
     */
    public function setRebate($rebate)
    {
        $this->rebate = $rebate;

        return $this;
    }

    /**
     * Get rebate
     *
     * @return string
     */
    public function getRebate()
    {
        return $this->rebate;
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
