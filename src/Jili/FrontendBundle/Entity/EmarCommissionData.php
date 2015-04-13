<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarCommissionData
 *
 * @ORM\Table(name="emar_commission_data")
 * @ORM\Entity
 */
class EmarCommissionData
{
    /**
     * @var integer
     *
     * @ORM\Column(name="emar_commission_id", type="integer", nullable=false)
     */
    private $emarCommissionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="commission_id", type="integer", nullable=false)
     */
    private $commissionId;

    /**
     * @var string
     *
     * @ORM\Column(name="commission_category", type="string", length=200, nullable=true)
     */
    private $commissionCategory;

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
     * @ORM\Column(name="product_apply_to", type="string", length=100, nullable=true)
     */
    private $productApplyTo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set emarCommissionId
     *
     * @param integer $emarCommissionId
     * @return EmarCommissionData
     */
    public function setEmarCommissionId($emarCommissionId)
    {
        $this->emarCommissionId = $emarCommissionId;

        return $this;
    }

    /**
     * Get emarCommissionId
     *
     * @return integer 
     */
    public function getEmarCommissionId()
    {
        return $this->emarCommissionId;
    }

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
     * Set commissionCategory
     *
     * @param string $commissionCategory
     * @return EmarCommissionData
     */
    public function setCommissionCategory($commissionCategory)
    {
        $this->commissionCategory = $commissionCategory;

        return $this;
    }

    /**
     * Get commissionCategory
     *
     * @return string 
     */
    public function getCommissionCategory()
    {
        return $this->commissionCategory;
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
