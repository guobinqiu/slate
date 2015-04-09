<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvertisementWebsites
 *
 * @ORM\Table(name="advertisement_websites", uniqueConstraints={@ORM\UniqueConstraint(name="ad_category_id", columns={"ad_category_id", "web_id"})}, indexes={@ORM\Index(name="web_url", columns={"web_url"})})
 * @ORM\Entity
 */
class AdvertisementWebsites
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_category_id", type="integer", nullable=false)
     */
    private $adCategoryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="web_id", type="integer", nullable=false)
     */
    private $webId;

    /**
     * @var string
     *
     * @ORM\Column(name="web_url", type="string", length=256, nullable=false)
     */
    private $webUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="web_name", type="string", length=64, nullable=false)
     */
    private $webName;

    /**
     * @var string
     *
     * @ORM\Column(name="web_category", type="string", length=64, nullable=false)
     */
    private $webCategory;

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
     * Set adCategoryId
     *
     * @param integer $adCategoryId
     * @return AdvertisementWebsites
     */
    public function setAdCategoryId($adCategoryId)
    {
        $this->adCategoryId = $adCategoryId;

        return $this;
    }

    /**
     * Get adCategoryId
     *
     * @return integer 
     */
    public function getAdCategoryId()
    {
        return $this->adCategoryId;
    }

    /**
     * Set webId
     *
     * @param integer $webId
     * @return AdvertisementWebsites
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
     * Set webUrl
     *
     * @param string $webUrl
     * @return AdvertisementWebsites
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
     * Set webName
     *
     * @param string $webName
     * @return AdvertisementWebsites
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
     * Set webCategory
     *
     * @param string $webCategory
     * @return AdvertisementWebsites
     */
    public function setWebCategory($webCategory)
    {
        $this->webCategory = $webCategory;

        return $this;
    }

    /**
     * Get webCategory
     *
     * @return string 
     */
    public function getWebCategory()
    {
        return $this->webCategory;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AdvertisementWebsites
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
