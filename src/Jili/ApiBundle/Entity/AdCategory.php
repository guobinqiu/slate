<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdCategory
 */
class AdCategory
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var varchar
     */
    private $category_name;


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
     * Set category_name
     *
     * @param \varchar $categoryName
     * @return AdCategory
     */
    public function setCategoryName(\varchar $categoryName)
    {
        $this->category_name = $categoryName;
    
        return $this;
    }

    /**
     * Get category_name
     *
     * @return \varchar 
     */
    public function getCategoryName()
    {
        return $this->category_name;
    }
}
