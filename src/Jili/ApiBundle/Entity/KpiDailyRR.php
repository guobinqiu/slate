<?php
namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KpiDailyRR
 *
 * @ORM\Table(name="kpi_daily_RR")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\KpiDailyRRRepository")
 */
class KpiDailyRR
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="kpi_YMD", type="string", length=10, nullable=true, options={"default": ""})
     */
    private $kpiYMD;

    /**
     * @var string
     *
     * @ORM\Column(name="register_YMD", type="string", length=10, nullable=true, options={"default": ""})
     */
    private $registerYMD;

    /**
     * @var integer
     *
     * @ORM\Column(name="RR_day", type="integer", nullable=true, options={"default": 0})
     */
    private $RRday;

    /**
     * @var integer
     *
     * @ORM\Column(name="register_user", type="integer", nullable=true, options={"default": 0})
     */
    private $registerUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="active_user", type="integer", nullable=true, options={"default": 0})
     */
    private $activeUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="RR", type="integer", nullable=true, options={"default": 0})
     */
    private $RR;

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
     * Set kpiYMD
     *
     * @param string $kpiYMD
     * @return KpiDailyRR
     */
    public function setKpiYMD($kpiYMD)
    {
        $this->kpiYMD = $kpiYMD;

        return $this;
    }

    /**
     * Get kpiYMD
     *
     * @return string
     */
    public function getKpiYMD()
    {
        return $this->kpiYMD;
    }

    /**
     * Set registerYMD
     *
     * @param string $registerYMD
     * @return KpiDailyRR
     */
    public function setRegisterYMD($registerYMD)
    {
        $this->registerYMD = $registerYMD;

        return $this;
    }

    /**
     * Get registerYMD
     *
     * @return string
     */
    public function getRegisterYMD()
    {
        return $this->registerYMD;
    }

    /**
     * Set RRday
     *
     * @param string $RRday
     * @return KpiDailyRR
     */
    public function setRRday($RRday)
    {
        $this->RRday = $RRday;

        return $this;
    }

    /**
     * Get RRday
     *
     * @return string
     */
    public function getRRday()
    {
        return $this->RRday;
    }

    /**
     * Set registerUser
     *
     * @param string $registerUser
     * @return KpiDailyRR
     */
    public function setRegisterUser($registerUser)
    {
        $this->registerUser = $registerUser;

        return $this;
    }

    /**
     * Get registerUser
     *
     * @return string
     */
    public function getRegisterUser()
    {
        return $this->registerUser;
    }

    /**
     * Set activeUser
     *
     * @param string $activeUser
     * @return KpiDailyRR
     */
    public function setActiveUser($activeUser)
    {
        $this->activeUser = $activeUser;

        return $this;
    }

    /**
     * Get activeUser
     *
     * @return string
     */
    public function getActiveUser()
    {
        return $this->activeUser;
    }

    /**
     * Set rr
     *
     * @param string $RR
     * @return KpiDailyRR
     */
    public function setRR($RR)
    {
        $this->RR = $RR;

        return $this;
    }

    /**
     * Get RR
     *
     * @return string
     */
    public function getRR()
    {
        return $this->RR;
    }
}
