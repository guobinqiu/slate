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
    private $rrday;

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
    private $rr;

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
     * Set rrday
     *
     * @param string $rrday
     * @return KpiDailyRR
     */
    public function setRRday($rrday)
    {
        $this->rrday = $rrday;

        return $this;
    }

    /**
     * Get rrday
     *
     * @return string
     */
    public function getRRday()
    {
        return $this->rrday;
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
     * @param string $rr
     * @return KpiDailyRR
     */
    public function setRR($rr)
    {
        $this->rr = $rr;

        return $this;
    }

    /**
     * Get rr
     *
     * @return string
     */
    public function getRR()
    {
        return $this->rr;
    }
}
