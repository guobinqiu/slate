<?php

namespace Jili\EmarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmarRequest
 *
 * @ORM\Table(name="emar_request")
 * @ORM\Entity(repositoryClass="Jili\EmarBundle\Repository\EmarRequestRepository")
 */
class EmarRequest
{
    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=16, nullable=false)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
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
     * Set tag
     *
     * @param string $tag
     * @return EmarRequest
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return EmarRequest
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
