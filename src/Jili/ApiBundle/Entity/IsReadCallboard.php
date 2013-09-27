<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IsReadCallboard
 *
 * @ORM\Table(name="is_read_callboard")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\IsReadCallboardRepository")
 */
class IsReadCallboard
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="send_cb_id", type="integer", nullable=true)
     */
    private $sendCbId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;



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
     * Set sendCbId
     *
     * @param integer $sendCbId
     * @return IsReadCallboard
     */
    public function setSendCbId($sendCbId)
    {
        $this->sendCbId = $sendCbId;
    
        return $this;
    }

    /**
     * Get sendCbId
     *
     * @return integer 
     */
    public function getSendCbId()
    {
        return $this->sendCbId;
    }


    /**
     * Set userId
     *
     * @param integer $userId
     * @return IsReadCallboard
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    
    
}
