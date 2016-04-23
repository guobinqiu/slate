<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarCommissionData
 *
 * @ORM\Table(name="emar_commission_data")
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GeneralCommissionDataRepository")
 */
class EmarCommissionData extends CommissionDataBase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="commission_id", type="integer", nullable=false, options={"comment":"FK"})
     */
    private $commissionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="commission_serial_number", type="integer", nullable=false, options={"comment":"佣金序号"})
     */
    private $commissionSerialNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_name", type="string", length=200, nullable=true, options={"default":"", "comment":"佣金类目"})
     */
    private $commissionName;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="string", length=100, nullable=true, options={"default":"", "comment":"佣金"})
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_period", type="string", length=100, nullable=true, options={"default":"", "comment":"佣金周期"})
     */
    private $commissionPeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="product_apply_to", type="string", length=100, nullable=true, options={"default":"", "comment":"适用商品"})
     */
    private $productApplyTo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true, options={"comment":"详细说明"})
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"comment":"写入时间"})
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set commissionId
     *
     * @param integer $commissionId
     * @return EmarCommissionData
     */
    public function setCommissionId($commissionId)
    {
        $this->commissionId = $commissionId;

        return $this;
    }

    /**
     * Get commissionId
     *
     * @return integer 
     */
    public function getCommissionId()
    {
        return $this->commissionId;
    }

    /**
     * Set commissionSerialNumber
     *
     * @param integer $commissionSerialNumber
     * @return EmarCommissionData
     */
    public function setCommissionSerialNumber($commissionSerialNumber)
    {
        $this->commissionSerialNumber = $commissionSerialNumber;

        return $this;
    }

    /**
     * Get commissionSerialNumber
     *
     * @return integer 
     */
    public function getCommissionSerialNumber()
    {
        return $this->commissionSerialNumber;
    }

    /**
     * Set commissionName
     *
     * @param string $commissionName
     * @return EmarCommissionData
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
     * @return EmarCommissionData
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
     * @return EmarCommissionData
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
     * Set productApplyTo
     *
     * @param string $productApplyTo
     * @return EmarCommissionData
     */
    public function setProductApplyTo($productApplyTo)
    {
        $this->productApplyTo = $productApplyTo;

        return $this;
    }

    /**
     * Get productApplyTo
     *
     * @return string 
     */
    public function getProductApplyTo()
    {
        return $this->productApplyTo;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return EmarCommissionData
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return EmarCommissionData
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
