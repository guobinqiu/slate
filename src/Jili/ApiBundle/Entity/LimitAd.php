<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LimitAd
 */
class LimitAd
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var varchar
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
     * @param \varchar $incentive
     * @return LimitAd
     */
    public function setIncentive(\varchar $incentive)
    {
        $this->incentive = $incentive;
    
        return $this;
    }

    /**
     * Get incentive
     *
     * @return \varchar 
     */
    public function getIncentive()
    {
        return $this->incentive;
    }
}
