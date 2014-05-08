<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdCategory
 *
 * @ORM\Table(name="ad_category")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdCategoryRepository")
 */
class AdCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category_name", type="string", length=45, nullable=true)
     */
    private $categoryName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="asp", type="string", length=64, nullable=true)
     */
    private $asp;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=100, nullable=true)
     */
    private $displayName;



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
     * Set categoryName
     *
     * @param string $categoryName
     * @return AdCategory
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    
        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string 
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * Set asp
     *
     * @param string $asp
     * @return AdCategory
     */
    public function setAsp($asp)
    {
        $this->asp = $asp;

        return $this;
    }

    /**
     * Get asp
     *
     * @return string 
     */
    public function getAsp()
    {
        return $this->asp;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return AdCategory
     */
    public function setDisplayName($displayName)
    {
    	$this->displayName = $displayName;
    
    	return $this;
    }
    
    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
    	return $this->displayName;
    }
}
