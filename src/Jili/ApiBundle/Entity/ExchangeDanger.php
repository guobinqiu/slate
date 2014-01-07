<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExchangeDanger
 *
 * @ORM\Table(name="exchange_danger")
 * @ORM\Entity
 */
class ExchangeDanger
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="exchange_id", type="integer")
     */
    private $exchangeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="danger_type", type="integer")
     */
    private $dangerType;

    /**
     * @var string
     *
     * @ORM\Column(name="danger_content", type="string",length=50)
     */
    private $dangerContent;

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
     * Set userId
     *
     * @param integer $userId
     * @return ExchangeDanger
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


    /**
     * Set exchangeId
     *
     * @param integer $exchangeId
     * @return ExchangeDanger
     */
    public function setExchangeId($exchangeId)
    {
        $this->exchangeId = $exchangeId;
    
        return $this;
    }

    /**
     * Get exchangeId
     *
     * @return integer 
     */
    public function getExchangeId()
    {
        return $this->exchangeId;
    }

    /**
     * Set dangerType
     *
     * @param integer $dangerType
     * @return ExchangeDanger
     */
    public function setDangerType($dangerType)
    {
        $this->dangerType = $dangerType;
    
        return $this;
    }

    /**
     * Get dangerType
     *
     * @return integer 
     */
    public function getDangerType()
    {
        return $this->dangerType;
    }


    /**
     * Set dangerContent
     *
     * @param string $dangerContent
     * @return ExchangeDanger
     */
    public function setDangerContent($dangerContent)
    {
    	$this->dangerContent = $dangerContent;
    
    	return $this;
    }
    
    /**
     * Get dangerContent
     *
     * @return string
     */
    public function getDangerContent()
    {
    	return $this->dangerContent;
    }

    
    
}