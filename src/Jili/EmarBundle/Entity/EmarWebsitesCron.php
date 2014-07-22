<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarWebsitesCron
 *
 * @ORM\Table(name="emar_websites_cron", uniqueConstraints={@ORM\UniqueConstraint(name="web_id", columns={"web_id"})})
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarWebsitesCronRepository")
 */
class EmarWebsitesCron
{
    /**
     * @var integer
     *
     * @ORM\Column(name="web_id", type="integer", nullable=false)
     */
    private $webId;

    /**
     * @var string
     *
     * @ORM\Column(name="web_name", type="string", length=128, nullable=true)
     */
    private $webName;

    /**
     * @var integer
     *
     * @ORM\Column(name="web_catid", type="integer", nullable=true)
     */
    private $webCatid;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_url", type="string", length=128, nullable=true)
     */
    private $logoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="web_url", type="string", length=255, nullable=true)
     */
    private $webUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="information", type="text", nullable=true)
     */
    private $information;

    /**
     * @var string
     *
     * @ORM\Column(name="begin_date", type="string", length=128, nullable=true)
     */
    private $beginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="end_date", type="string", length=128, nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="text", nullable=true)
     */
    private $commission;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set webId
     *
     * @param integer $webId
     * @return EmarWebsitesCron
     */
    public function setWebId($webId)
    {
        $this->webId = $webId;

        return $this;
    }

    /**
     * Get webId
     *
     * @return integer
     */
    public function getWebId()
    {
        return $this->webId;
    }

    /**
     * Set webName
     *
     * @param string $webName
     * @return EmarWebsitesCron
     */
    public function setWebName($webName)
    {
        $this->webName = $webName;

        return $this;
    }

    /**
     * Get webName
     *
     * @return string
     */
    public function getWebName()
    {
        return $this->webName;
    }

    /**
     * Set webCatid
     *
     * @param integer $webCatid
     * @return EmarWebsitesCron
     */
    public function setWebCatid($webCatid)
    {
        $this->webCatid = $webCatid;

        return $this;
    }

    /**
     * Get webCatid
     *
     * @return integer
     */
    public function getWebCatid()
    {
        return $this->webCatid;
    }

    /**
     * Set logoUrl
     *
     * @param string $logoUrl
     * @return EmarWebsitesCron
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    /**
     * Get logoUrl
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * Set webUrl
     *
     * @param string $webUrl
     * @return EmarWebsitesCron
     */
    public function setWebUrl($webUrl)
    {
        $this->webUrl = $webUrl;

        return $this;
    }

    /**
     * Get webUrl
     *
     * @return string
     */
    public function getWebUrl()
    {
        return $this->webUrl;
    }

    /**
     * Set information
     *
     * @param string $information
     * @return EmarWebsitesCron
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Get information
     *
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * Set beginDate
     *
     * @param string $beginDate
     * @return EmarWebsitesCron
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;

        return $this;
    }

    /**
     * Get beginDate
     *
     * @return string
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param string $endDate
     * @return EmarWebsitesCron
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set commission
     *
     * @param string $commission
     * @return EmarWebsitesCron
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
