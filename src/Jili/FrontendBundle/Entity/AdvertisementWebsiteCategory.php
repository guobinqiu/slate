<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvertisementWebsiteCategory
 *
 * @ORM\Table(name="advertisement_website_category")
 * @ORM\Entity
 */
class AdvertisementWebsiteCategory
{
    /**
     * @var string
     *
     * @ORM\Column(name="cat_name", type="string", length=64, nullable=false)
     */
    private $catName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set catName
     *
     * @param string $catName
     * @return AdvertisementWebsiteCategory
     */
    public function setCatName($catName)
    {
        $this->catName = $catName;

        return $this;
    }

    /**
     * Get catName
     *
     * @return string 
     */
    public function getCatName()
    {
        return $this->catName;
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
