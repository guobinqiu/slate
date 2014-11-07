<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaobaoRecommend
 *
 * @ORM\Table(name="taobao_recommend")
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\TaobaoRecommendRepository")
 */
class TaobaoRecommend
{
    /**
     * @var string
     *
     * @ORM\Column(name="component_ids", type="string", length=255, nullable=false)
     */
    private $componentIds;

    /**
     * @var string
     *
     * @ORM\Column(name="recommend_name", type="string", length=64, nullable=false)
     */
    private $recommendName;

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
     * Set componentIds
     *
     * @param string $componentIds
     * @return TaobaoRecommend
     */
    public function setComponentIds($componentIds)
    {
        $this->componentIds = $componentIds;

        return $this;
    }

    /**
     * Get componentIds
     *
     * @return string 
     */
    public function getComponentIds()
    {
        return $this->componentIds;
    }

    /**
     * Set recommendName
     *
     * @param string $recommendName
     * @return TaobaoRecommend
     */
    public function setRecommendName($recommendName)
    {
        $this->recommendName = $recommendName;

        return $this;
    }

    /**
     * Get recommendName
     *
     * @return string 
     */
    public function getRecommendName()
    {
        return $this->recommendName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return TaobaoRecommend
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
     * @return TaobaoRecommend
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
