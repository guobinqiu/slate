<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarWebsites
 *
 * @ORM\Table(name="emar_websites", uniqueConstraints={@ORM\UniqueConstraint(name="web_id", columns={"web_id"})}, indexes={@ORM\Index(name="web_catid", columns={"web_catid"})})
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarWebsitesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EmarWebsites
{
    /**
     * @var integer
     *
     * @ORM\Column(name="web_id", type="integer", nullable=false, options={"comment":"商家网站的站点ID"})
     */
    private $webId;

    /**
     * @var integer
     *
     * @ORM\Column(name="web_catid", type="integer", nullable=true, options={"comment":"商家网站所属分类的分类id"})
     */
    private $webCatid;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="string", length=128, nullable=true, options={"default":"", "comment":" 推广佣金比例信息"})
     */
    private $commission;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true, options={"default":0, "comment":"是否已经弃用, 0:末弃用"})
     */
    private $isDeleted;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true, options={"default":NULL, "comment":"商家网站显示的顺序"})
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_hidden", type="boolean", nullable=true, options={"default":1, "comment":"是否不显示, 1:不用做页面显示"})
     */
    private $isHidden;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_hot", type="boolean", nullable=true, options={"default":0, "comment":"是否为热卖商家"})
     */
    private $isHot;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hot_at", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00", "comment":"热卖商家 排序"})
     */
    private $hotAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
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
     * Set webId
     *
     * @param integer $webId
     * @return EmarWebsites
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
     * Set webCatid
     *
     * @param integer $webCatid
     * @return EmarWebsites
     */
    public function setWebCatid($webCatid)
    {
        $this->webCatid = $webCatid;

        return $this;
    }

    /**
     * Get webCatid
     *
     * @return integer
     */
    public function getWebCatid()
    {
        return $this->webCatid;
    }

    /**
     * Set commission
     *
     * @param string $commission
     * @return EmarWebsites
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return EmarWebsites
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return EmarWebsites
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set isHidden
     *
     * @param boolean $isHidden
     * @return EmarWebsites
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     *
     * @return boolean
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set isHot
     *
     * @param boolean $isHot
     * @return EmarWebsites
     */
    public function setIsHot($isHot)
    {
        $this->isHot = $isHot;

        return $this;
    }

    /**
     * Get isHot
     *
     * @return boolean
     */
    public function getIsHot()
    {
        return $this->isHot;
    }

    /**
     * Set hotAt
     *
     * @param \DateTime $hotAt
     * @return EmarWebsites
     */
    public function setHotAt($hotAt)
    {
        $this->hotAt = $hotAt;

        return $this;
    }

    /**
     * Get hotAt
     *
     * @return \DateTime
     */
    public function getHotAt()
    {
        return $this->hotAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return EmarWebsites
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
     * @return EmarWebsites
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(is_null( $this->getIsDeleted() )) {
            $this->setIsDeleted( 0 );
        }

        if(is_null( $this->getIsHidden() )) {
            $this->setIsHidden( 1 );
        }

        if(is_null( $this->getIsHot() )) {
            $this->setIsHidden( 0 );
        }
        $this->hotAt= new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
        if( $this->getIsHot() == 1 ) {
            $this->hotAt= new \DateTime();
        }
    }
}
