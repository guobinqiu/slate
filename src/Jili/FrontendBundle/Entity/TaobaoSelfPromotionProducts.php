<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaobaoSelfPromotionProducts
 *
 * @ORM\Table(name="taobao_self_promotion_products", indexes={@ORM\Index(name="taobao_category_id", columns={"taobao_category_id"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\TaobaoSelfPromotionProductsRepository")
 */
class TaobaoSelfPromotionProducts
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, nullable=true)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=9, scale=2, nullable=false)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="price_promotion", type="float", precision=9, scale=2, nullable=false)
     */
    private $pricePromotion;

    /**
     * @var string
     *
     * @ORM\Column(name="item_url", type="string", length=255, nullable=true)
     */
    private $itemUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="click_url", type="text", nullable=false)
     */
    private $clickUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="picture_name", type="string", length=64, nullable=true)
     */
    private $pictureName;

    /**
     * @var string
     *
     * @ORM\Column(name="comment_description", type="string", length=255, nullable=true)
     */
    private $commentDescription;

    /**
     * @var float
     *
     * @ORM\Column(name="promotion_rate", type="float", precision=9, scale=2, nullable=false)
     */
    private $promotionRate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
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
     * @var \Jili\FrontendBundle\Entity\TaobaoCategory
     *
     * @ORM\ManyToOne(targetEntity="Jili\FrontendBundle\Entity\TaobaoCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taobao_category_id", referencedColumnName="id")
     * })
     */
    private $taobaoCategory;



    /**
     * Set title
     *
     * @param string $title
     * @return TaobaoSelfPromotionProducts
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return TaobaoSelfPromotionProducts
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set pricePromotion
     *
     * @param float $pricePromotion
     * @return TaobaoSelfPromotionProducts
     */
    public function setPricePromotion($pricePromotion)
    {
        $this->pricePromotion = $pricePromotion;

        return $this;
    }

    /**
     * Get pricePromotion
     *
     * @return float 
     */
    public function getPricePromotion()
    {
        return $this->pricePromotion;
    }

    /**
     * Set itemUrl
     *
     * @param string $itemUrl
     * @return TaobaoSelfPromotionProducts
     */
    public function setItemUrl($itemUrl)
    {
        $this->itemUrl = $itemUrl;

        return $this;
    }

    /**
     * Get itemUrl
     *
     * @return string 
     */
    public function getItemUrl()
    {
        return $this->itemUrl;
    }

    /**
     * Set clickUrl
     *
     * @param string $clickUrl
     * @return TaobaoSelfPromotionProducts
     */
    public function setClickUrl($clickUrl)
    {
        $this->clickUrl = $clickUrl;

        return $this;
    }

    /**
     * Get clickUrl
     *
     * @return string 
     */
    public function getClickUrl()
    {
        return $this->clickUrl;
    }

    /**
     * Set pictureName
     *
     * @param string $pictureName
     * @return TaobaoSelfPromotionProducts
     */
    public function setPictureName($pictureName)
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    /**
     * Get pictureName
     *
     * @return string 
     */
    public function getPictureName()
    {
        return $this->pictureName;
    }

    /**
     * Set commentDescription
     *
     * @param string $commentDescription
     * @return TaobaoSelfPromotionProducts
     */
    public function setCommentDescription($commentDescription)
    {
        $this->commentDescription = $commentDescription;

        return $this;
    }

    /**
     * Get commentDescription
     *
     * @return string 
     */
    public function getCommentDescription()
    {
        return $this->commentDescription;
    }

    /**
     * Set promotionRate
     *
     * @param float $promotionRate
     * @return TaobaoSelfPromotionProducts
     */
    public function setPromotionRate($promotionRate)
    {
        $this->promotionRate = $promotionRate;

        return $this;
    }

    /**
     * Get promotionRate
     *
     * @return float 
     */
    public function getPromotionRate()
    {
        return $this->promotionRate;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return TaobaoSelfPromotionProducts
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return TaobaoSelfPromotionProducts
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

    /**
     * Set taobaoCategory
     *
     * @param \Jili\FrontendBundle\Entity\TaobaoCategory $taobaoCategory
     * @return TaobaoSelfPromotionProducts
     */
    public function setTaobaoCategory(\Jili\FrontendBundle\Entity\TaobaoCategory $taobaoCategory = null)
    {
        $this->taobaoCategory = $taobaoCategory;

        return $this;
    }

    /**
     * Get taobaoCategory
     *
     * @return \Jili\FrontendBundle\Entity\TaobaoCategory 
     */
    public function getTaobaoCategory()
    {
        return $this->taobaoCategory;
    }

    /**
     *
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime())
            ->setPromotionRate(0);
    }

}
