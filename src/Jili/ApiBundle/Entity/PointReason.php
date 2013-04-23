<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PointReason
 */
class PointReason
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var varchar
     */
    private $reason;


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
     * Set reason
     *
     * @param \varchar $reason
     * @return PointReason
     */
    public function setReason(\varchar $reason)
    {
        $this->reason = $reason;
    
        return $this;
    }

    /**
     * Get reason
     *
     * @return \varchar 
     */
    public function getReason()
    {
        return $this->reason;
    }
}
