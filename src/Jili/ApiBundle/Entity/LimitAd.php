<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LimitAd
 *
 * @ORM\Table(name="limit_ad")
 * @ORM\Entity
 */
class LimitAd
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
     * @var string
     *
     * @ORM\Column(name="incentive", type="string", length=45, nullable=false)
     */
    private $incentive;



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
     * Set incentive
     *
     * @param string $incentive
     * @return LimitAd
     */
    public function setIncentive($incentive)
    {
        $this->incentive = $incentive;
    
        return $this;
    }

    /**
     * Get incentive
     *
     * @return string 
     */
    public function getIncentive()
    {
        return $this->incentive;
    }
}