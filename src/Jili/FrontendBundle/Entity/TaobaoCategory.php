<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaobaoCategory
 *
 * @ORM\Table(name="taobao_category")
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\TaobaoCategoryRepository")
 */
class TaobaoCategory
{
    const SELF_PROMOTION=1;
    const COMPONENTS=2;
    /**
     * @var string
     *
     * @ORM\Column(name="category_name", type="string", length=16, nullable=false)
     */
    private $categoryName;

    /**
     * @var integer
     *
     * @ORM\Column(name="union_product", type="integer", nullable=true)
     */
    private $unionProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=false)
     */
    private $deleteFlag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set categoryName
     *
     * @param string $categoryName
     * @return TaobaoCategory
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
     * Set unionProduct
     *
     * @param integer $unionProduct
     * @return TaobaoCategory
     */
    public function setUnionProduct($unionProduct)
    {
        $this->unionProduct = $unionProduct;

        return $this;
    }

    /**
     * Get unionProduct
     *
     * @return integer 
     */
    public function getUnionProduct()
    {
        return $this->unionProduct;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return TaobaoCategory
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;

        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return integer
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return TaobaoCategory
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return TaobaoCategory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
