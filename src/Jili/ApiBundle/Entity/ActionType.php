<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActionType
 */
class ActionType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var varchar
     */
    private $action_type;


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
     * Set action_type
     *
     * @param \varchar $actionType
     * @return ActionType
     */
    public function setActionType(\varchar $actionType)
    {
        $this->action_type = $actionType;
    
        return $this;
    }

    /**
     * Get action_type
     *
     * @return \varchar 
     */
    public function getActionType()
    {
        return $this->action_type;
    }
}
