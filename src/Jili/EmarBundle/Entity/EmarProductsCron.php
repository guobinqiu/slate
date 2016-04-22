<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarProductsCron
 *
 * @ORM\Table(name="emar_products_cron", uniqueConstraints={@ORM\UniqueConstraint(name="pid", columns={"pid"})}, indexes={@ORM\Index(name="wid_catid", columns={"web_id", "catid"})})
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarProductsCronRepository")
 */
class EmarProductsCron
{
    /**
     * @var integer
     *
     * @ORM\Column(name="pid", type="integer", nullable=false, options={"comment":"商品id"})
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(name="p_name", type="string", length=128, nullable=true, options={"default":"", "comment":"商品名称"})
     */
    private $pName;

    /**
     * @var integer
     *
     * @ORM\Column(name="web_id", type="integer", nullable=false, options={"comment":"商家网站的站点ID"})
     */
    private $webId;

    /**
     * @var string
     *
     * @ORM\Column(name="web_name", type="string", length=128, nullable=true, options={"default":"", "comment":"商家网站名称"})
     */
    private $webName;

    /**
     * @var string
     *
     * @ORM\Column(name="ori_price", type="string", length=128, nullable=true, options={"default":"0.0", "comment":"参考价格，原始价格"})
     */
    private $oriPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="cur_price", type="string", length=128, nullable=true, options={"default":"0.0", "comment":"实际价格，现在价格"})
     */
    private $curPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="pic_url", type="string", length=128, nullable=true, options={"default":"", "comment":"图片链接"})
     */
    private $picUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="catid", type="integer", nullable=false, options={"comment":"商品分类id"})
     */
    private $catid;

    /**
     * @var string
     *
     * @ORM\Column(name="cname", type="string", length=128, nullable=true, options={"default":"", "comment":"商品分类名称"})
     */
    private $cname;

    /**
     * @var string
     *
     * @ORM\Column(name="p_o_url", type="string", length=128, nullable=true, options={"default":"", "comment":"商品的计费链接"})
     */
    private $pOUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="short_intro", type="text", nullable=true, options={"comment":"商品详情"})
     */
    private $shortIntro;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set pid
     *
     * @param integer $pid
     * @return EmarProductsCron
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set pName
     *
     * @param string $pName
     * @return EmarProductsCron
     */
    public function setPName($pName)
    {
        $this->pName = $pName;

        return $this;
    }

    /**
     * Get pName
     *
     * @return string
     */
    public function getPName()
    {
        return $this->pName;
    }

    /**
     * Set webId
     *
     * @param integer $webId
     * @return EmarProductsCron
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
     * @return EmarProductsCron
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
     * Set oriPrice
     *
     * @param string $oriPrice
     * @return EmarProductsCron
     */
    public function setOriPrice($oriPrice)
    {
        $this->oriPrice = $oriPrice;

        return $this;
    }

    /**
     * Get oriPrice
     *
     * @return string
     */
    public function getOriPrice()
    {
        return $this->oriPrice;
    }

    /**
     * Set curPrice
     *
     * @param string $curPrice
     * @return EmarProductsCron
     */
    public function setCurPrice($curPrice)
    {
        $this->curPrice = $curPrice;

        return $this;
    }

    /**
     * Get curPrice
     *
     * @return string
     */
    public function getCurPrice()
    {
        return $this->curPrice;
    }

    /**
     * Set picUrl
     *
     * @param string $picUrl
     * @return EmarProductsCron
     */
    public function setPicUrl($picUrl)
    {
        $this->picUrl = $picUrl;

        return $this;
    }

    /**
     * Get picUrl
     *
     * @return string
     */
    public function getPicUrl()
    {
        return $this->picUrl;
    }

    /**
     * Set catid
     *
     * @param integer $catid
     * @return EmarProductsCron
     */
    public function setCatid($catid)
    {
        $this->catid = $catid;

        return $this;
    }

    /**
     * Get catid
     *
     * @return integer
     */
    public function getCatid()
    {
        return $this->catid;
    }

    /**
     * Set cname
     *
     * @param string $cname
     * @return EmarProductsCron
     */
    public function setCname($cname)
    {
        $this->cname = $cname;

        return $this;
    }

    /**
     * Get cname
     *
     * @return string
     */
    public function getCname()
    {
        return $this->cname;
    }

    /**
     * Set pOUrl
     *
     * @param string $pOUrl
     * @return EmarProductsCron
     */
    public function setPOUrl($pOUrl)
    {
        $this->pOUrl = $pOUrl;

        return $this;
    }

    /**
     * Get pOUrl
     *
     * @return string
     */
    public function getPOUrl()
    {
        return $this->pOUrl;
    }

    /**
     * Set shortIntro
     *
     * @param string $shortIntro
     * @return EmarProductsCron
     */
    public function setShortIntro($shortIntro)
    {
        $this->shortIntro = $shortIntro;

        return $this;
    }

    /**
     * Get shortIntro
     *
     * @return string
     */
    public function getShortIntro()
    {
        return $this->shortIntro;
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
