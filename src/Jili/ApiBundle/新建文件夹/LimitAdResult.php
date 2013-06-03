<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LimitAdResult
 *
 * @ORM\Table(name="limit_ad_result")
 * @ORM\Entity
 */
class LimitAdResult
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_ad_id", type="integer", nullable=false)
     */
    private $limitAdId;

    /**
     * @var string
     *
     * @ORM\Column(name="result_point", type="string", length=45, nullable=true)
     */
    private $resultPoint;



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
     * Set limitAdId
     *
     * @param integer $limitAdId
     * @return LimitAdResult
     */
    public function setLimitAdId($limitAdId)
    {
        $this->limitAdId = $limitAdId;
    
        return $this;
    }

    /**
     * Get limitAdId
     *
     * @return integer 
     */
    public function getLimitAdId()
    {
        return $this->limitAdId;
    }

    /**
     * Set resultPoint
     *
     * @param string $resultPoint
     * @return LimitAdResult
     */
    public function setResultPoint($resultPoint)
    {
        $this->resultPoint = $resultPoint;
    
        return $this;
    }

    /**
     * Get resultPoint
     *
     * @return string 
     */
    public function getResultPoint()
    {
        return $this->resultPoint;
    }
}
