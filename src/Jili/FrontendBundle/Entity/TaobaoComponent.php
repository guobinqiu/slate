<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaobaoComponent
 *
 * @ORM\Table(name="taobao_component")
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\TaobaoComponentRepository")
 */
class TaobaoComponent
{
    const TAOBAO_COMPONENT_SEARCH_BOX =1; //搜索框
    const TAOBAO_COMPONENT_KEYWORD = 2; //分类产品,关键字
    const TAOBAO_COMPONENT_ITEM = 3; //单品
    const TAOBAO_COMPONENT_SHOP = 4; //店铺

    /**
     * @var integer
     *
     * @ORM\Column(name="component_id", type="integer", nullable=false)
     */
    private $componentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", nullable=true)
     */
    private $categoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", length=255, nullable=true)
     */
    private $keyword;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sort", type="boolean", nullable=true)
     */
    private $sort;

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
     * Set componentId
     *
     * @param integer $componentId
     * @return TaobaoComponent
     */
    public function setComponentId($componentId)
    {
        $this->componentId = $componentId;

        return $this;
    }

    /**
     * Get componentId
     *
     * @return integer
     */
    public function getComponentId()
    {
        return $this->componentId;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return TaobaoComponent
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
     * Set keyword
     *
     * @param string $keyword
     * @return TaobaoComponent
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return TaobaoComponent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set sort
     *
     * @param boolean $sort
     * @return TaobaoComponent
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return boolean
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return TaobaoComponent
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
     * @return TaobaoComponent
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
