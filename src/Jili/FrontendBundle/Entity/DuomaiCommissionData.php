<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DuomaiCommissionData
 *
 * @ORM\Table(name="duomai_commission_data")
 * @ORM\Entity
 */
class DuomaiCommissionData extends CommissionDataBase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="commission_id", type="integer", nullable=false)
     */
    private $commissionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="commission_serial_number", type="integer", nullable=false)
     */
    private $commissionSerialNumber;

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
     * Set commissionId
     *
     * @param integer $commissionId
     * @return DuomaiCommissionData
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
     * @return DuomaiCommissionData
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
     * @return DuomaiCommissionData
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
     * @return DuomaiCommissionData
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
     * @return DuomaiCommissionData
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
     * Set description
     *
     * @param string $description
     * @return DuomaiCommissionData
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
     * @return DuomaiCommissionData
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
