<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExchangeAmazonResult
 *
 * @ORM\Table(name="exchange_amazon_result")
 * @ORM\Entity
 */
class ExchangeAmazonResult
{
    public function __construct()
    {
        $this->createtime = new \DateTime();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="exchange_id", type="integer", nullable=true)
     */
    private $exchangeId;

    /**
     * @var string
     *
     * @ORM\Column(name="amazonCard_one", type="string", length=50, nullable=true)
     */
    private $amazonCardOne;

    /**
     * @var string
     *
     * @ORM\Column(name="amazonCard_two", type="string", length=50, nullable=true)
     */
    private $amazonCardTwo;

    /**
     * @var string
     *
     * @ORM\Column(name="amazonCard_three", type="string", length=50, nullable=true)
     */
    private $amazonCardThree;

    /**
     * @var string
     *
     * @ORM\Column(name="amazonCard_four", type="string", length=50, nullable=true)
     */
    private $amazonCardFour;

    /**
     * @var string
     *
     * @ORM\Column(name="amazonCard_five", type="string", length=50, nullable=true)
     */
    private $amazonCardFive;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="createtime", type="datetime", nullable=true)
     */
    private $createtime;

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
     * Set exchangeId
     *
     * @param integer $exchangeId
     * @return ExchangeAmazonResult
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
     * Set amazonCardOne
     *
     * @param string $amazonCardOne
     * @return ExchangeAmazonResult
     */
    public function setAmazonCardOne($amazonCardOne)
    {
        $this->amazonCardOne = $amazonCardOne;

        return $this;
    }

    /**
     * Get amazonCardOne
     *
     * @return string
     */
    public function getAmazonCardOne()
    {
        return $this->amazonCardOne;
    }

    /**
     * Set amazonCardTwo
     *
     * @param string $amazonCardTwo
     * @return ExchangeAmazonResult
     */
    public function setAmazonCardTwo($amazonCardTwo)
    {
        $this->amazonCardTwo = $amazonCardTwo;

        return $this;
    }

    /**
     * Get amazonCardTwo
     *
     * @return string
     */
    public function getAmazonCardTwo()
    {
        return $this->amazonCardTwo;
    }

     /**
     * Set amazonCardThree
     *
     * @param string $amazonCardThree
     * @return ExchangeAmazonResult
     */
    public function setAmazonCardThree($amazonCardThree)
    {
        $this->amazonCardThree = $amazonCardThree;

        return $this;
    }

    /**
     * Get amazonCardThree
     *
     * @return string
     */
    public function getAmazonCardThree()
    {
        return $this->amazonCardThree;
    }

     /**
     * Set amazonCardFour
     *
     * @param string $amazonCardFour
     * @return ExchangeAmazonResult
     */
    public function setAmazonCardFour($amazonCardFour)
    {
        $this->amazonCardFour = $amazonCardFour;

        return $this;
    }

    /**
     * Get amazonCardFour
     *
     * @return string
     */
    public function getAmazonCardFour()
    {
        return $this->amazonCardFour;
    }

     /**
     * Set amazonCardFive
     *
     * @param string $amazonCardFive
     * @return ExchangeAmazonResult
     */
    public function setAmazonCardFive($amazonCardFive)
    {
        $this->amazonCardFive = $amazonCardFive;

        return $this;
    }

    /**
     * Get amazonCardFive
     *
     * @return string
     */
    public function getAmazonCardFive()
    {
        return $this->amazonCardFive;
    }

    /**
     * Set createtime
     *
     * @param \DateTime $createtime
     * @return ExchangeAmazonResult
     */
    public function setCreatetime($createtime)
    {
        $this->createtime = $createtime;

        return $this;
    }

    /**
     * Get createtime
     *
     * @return \DateTime
     */
    public function getCreatetime()
    {
        return $this->createtime;
    }


}
