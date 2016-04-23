<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarWebsitesCategory
 *
 * @ORM\Table(name="emar_websites_category", uniqueConstraints={@ORM\UniqueConstraint(name="web_id", columns={"web_id", "category_id"})})
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarWebsitesCategoryRepository")
 */
class EmarWebsitesCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="web_id", type="integer", nullable=false, options={"comment":"商家网站的id"})
     */
    private $webId;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false, options={"comment":"商品分类"})
     */
    private $categoryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer", nullable=false, options={"default":0,"comment":"计数"})
     */
    private $count;

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
     * @return EmarWebsitesCategory
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
     * Set categoryId
     *
     * @param integer $categoryId
     * @return EmarWebsitesCategory
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return EmarWebsitesCategory
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
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
