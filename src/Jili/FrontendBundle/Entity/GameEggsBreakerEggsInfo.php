<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameEggsBreakerEggsInfo
 *
 * @ORM\Table(name="game_eggs_breaker_eggs_info", indexes={@ORM\Index(name="user_visit_token", columns={"user_id", "token"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GameEggsBreakerEggsInfoRepository")
 */
class GameEggsBreakerEggsInfo
{
    const EGG_TYPE_COMMON = 1;
    const EGG_TYPE_CONSOLATION = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var float
     *
     * @ORM\Column(name="offcut_for_next", type="float", precision=9, scale=2, nullable=false)
     */
    private $offcutForNext;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_of_common", type="integer", nullable=false)
     */
    private $numOfCommon;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_of_consolation", type="integer", nullable=false)
     */
    private $numOfConsolation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="num_updated_at", type="datetime", nullable=true)
     */
    private $numUpdatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=32, nullable=false)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="token_updated_at", type="datetime", nullable=true)
     */
    private $tokenUpdatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

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
        $this->setNumOfCommon(0)
            ->setNumOfConsolation(0)
            ->setCreatedAt(new \DateTime());
    }

    /**
     * 
     */
    public function refreshToken( $seed = '') 
    {
        $seed .= $this->getUserId();
        $seed .= time();  
        $token = md5($seed );
        return $this->setToken($token);
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return GameEggsBreakerEggsInfo
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
     * Set offcutForNext
     *
     * @param float $offcutForNext
     * @return GameEggsBreakerEggsInfo
     */
    public function setOffcutForNext($offcutForNext)
    {
        $this->offcutForNext = $offcutForNext;

        return $this;
    }

    /**
     * Get offcutForNext
     *
     * @return float 
     */
    public function getOffcutForNext()
    {
        return $this->offcutForNext;
    }

    /**
     * Set numOfCommon
     *
     * @param integer $numOfCommon
     * @return GameEggsBreakerEggsInfo
     */
    public function setNumOfCommon($numOfCommon)
    {
        $this->numOfCommon = $numOfCommon;

        return $this;
    }

    /**
     * Get numOfCommon
     *
     * @return integer 
     */
    public function getNumOfCommon()
    {
        return $this->numOfCommon;
    }

    /**
     * Set numOfConsolation
     *
     * @param integer $numOfConsolation
     * @return GameEggsBreakerEggsInfo
     */
    public function setNumOfConsolation($numOfConsolation)
    {
        $this->numOfConsolation = $numOfConsolation;

        return $this;
    }

    /**
     * Get numOfConsolation
     *
     * @return integer 
     */
    public function getNumOfConsolation()
    {
        return $this->numOfConsolation;
    }

    /**
     * Set numUpdatedAt
     *
     * @param \DateTime $numUpdatedAt
     * @return GameEggsBreakerEggsInfo
     */
    public function setNumUpdatedAt($numUpdatedAt)
    {
        $this->numUpdatedAt = $numUpdatedAt;

        return $this;
    }

    /**
     * Get numUpdatedAt
     *
     * @return \DateTime 
     */
    public function getNumUpdatedAt()
    {
        return $this->numUpdatedAt;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return GameEggsBreakerEggsInfo
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tokenUpdatedAt
     *
     * @param \DateTime $tokenUpdatedAt
     * @return GameEggsBreakerEggsInfo
     */
    public function setTokenUpdatedAt($tokenUpdatedAt)
    {
        $this->tokenUpdatedAt = $tokenUpdatedAt;

        return $this;
    }

    /**
     * Get tokenUpdatedAt
     *
     * @return \DateTime 
     */
    public function getTokenUpdatedAt()
    {
        return $this->tokenUpdatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return GameEggsBreakerEggsInfo
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
