<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameEggsBreakerEggsInfo
 *
 * @ORM\Table(name="game_eggs_breaker_eggs_info", uniqueConstraints={@ORM\UniqueConstraint(name="user", columns={"user_id"})}, indexes={@ORM\Index(name="user_visit_token", columns={"user_id", "token"})})
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\GameEggsBreakerEggsInfoRepository")
 */
class GameEggsBreakerEggsInfo
{
    const EGG_TYPE_COMMON = 1;
    const EGG_TYPE_CONSOLATION = 2;
    const COST_PER_EGG = 10.00;
    const TOKEN_LENGTH = 32;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var float
     *
     * @ORM\Column(name="total_paid", type="float", precision=9, scale=2, nullable=false)
     */
    private $totalPaid;

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
     * Set totalPaid
     *
     * @param float $totalPaid
     * @return GameEggsBreakerEggsInfo
     */
    public function setTotalPaid($totalPaid)
    {
        $this->totalPaid = $totalPaid;

        return $this;
    }

    /**
     * Get totalPaid
     *
     * @return float 
     */
    public function getTotalPaid()
    {
        return $this->totalPaid;
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

    public function __construct()
    {
        $this->setNumOfCommon(0)
            ->setNumOfConsolation(0)
            ->setOffcutForNext(0)
            ->setTotalPaid(0)
            ->setCreatedAt(new \DateTime());
    }

    /**
     * @param array $eggs = array('common'=> , 'consolation'=> )
     * @param string $token ;
     */
    public function updateNumOfEggs( $eggs, $token ) 
    {
        if( $token !== $this->getToken()) {
            return ;
        } 
        if( isset($eggs['offcut']) ) {
            $this->setOffcutForNext($eggs['offcut']);
        }

        if( isset($eggs['paid']) ) {
            $this->setTotalPaid($eggs['paid']);
        }

        if( isset($eggs['common']) ) {
            $this->setNumOfCommon($eggs['common']);
        }

        if( isset($eggs['consolation']) ) {
            $consolation = $eggs['consolation'] + $this->getNumOfConsolation();
            $this->setNumOfConsolation($consolation);
        }

        return $this->setNumUpdatedAt( new \DateTime() );
    }

    /**
     * 
     */
    public function refreshToken( $seed = '') 
    {
        $seed .= $this->getUserId();
        $seed .= time();  
        $token = md5($seed );
        return $this->setToken($token)
            ->setTokenUpdatedAt(new \DateTime());
    }

    /**
     */
    public function getLessForNextEgg()
    {
        if(self::COST_PER_EGG <= floatval($this->getOffcutForNext())) {
            return 0;
        }
        return round( self::COST_PER_EGG - floatval($this->getOffcutForNext()), 2);
    }

    /**
     * @abstract update the entity and return the type of egg.
     * @return array array( egg_type = 1 );
     */
    public function getEggTypeByRandom()
    {
        $num_of_consolation = (int) $this->getNumOfConsolation();
        $num_of_common  = (int) $this->getNumOfCommon();

        if( $num_of_consolation >  0) {
            $pool = array_fill(0, $num_of_consolation, self::EGG_TYPE_CONSOLATION);
        } else {
            $pool = array();
        }

        if($num_of_common >  0) {
            if( count($pool ) > 0) {
                $pool = array_merge($pool, array_fill(0, $num_of_common,self::EGG_TYPE_COMMON));
            } else {
                $pool = array_fill(0, $num_of_common, self::EGG_TYPE_COMMON);
            }
        }

        if(count($pool) === 0 ) {
            return -1;
        }

        $key = array_rand($pool);
        $egg_type = $pool[$key];

        if($egg_type === self::EGG_TYPE_CONSOLATION ) {
            $this->setNumOfConsolation($num_of_consolation - 1 );
        } else {
            $this->setNumOfCommon($num_of_common- 1 );
        }
        $this->setNumUpdatedAt(new \Datetime());
        return $egg_type;
    }

}
