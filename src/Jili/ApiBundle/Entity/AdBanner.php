<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdBanner
 *
 * @ORM\Table(name="ad_banner")
 * @ORM\Entity()
 */
class AdBanner
{
	public function __construct() {
		$this->createTime = new \DateTime();
	}
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var string
     *
     * @ORM\Column(name="icon_image", type="string", length=250)
     */
    private $iconImage;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="ad_url", type="string", length=250)
     */
    private $adUrl;


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
     * Set iconImage
     *
     * @param string $iconImage
     * @return AdBanner
     */
    public function setIconImage($iconImage)
    {
    	$this->iconImage = $iconImage;
    
    	return $this;
    }
    
    /**
     * Get iconImage
     *
     * @return string
     */
    public function getIconImage()
    {
    	return $this->iconImage;
    }
    
    
    /**
     * Set adUrl
     *
     * @param string $adUrl
     * @return AdBanner
     */
    public function setAdUrl($adUrl)
    {
    	$this->adUrl = $adUrl;
    
    	return $this;
    }
    
    /**
     * Get adUrl
     *
     * @return string
     */
    public function getAdUrl()
    {
    	return $this->adUrl;
    }
    
    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return AdBanner
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
    
}
