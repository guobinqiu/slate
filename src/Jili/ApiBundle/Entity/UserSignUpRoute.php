<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSignUpRoute
 *
 * @ORM\Table(name="user_sign_up_route", indexes={ @ORM\Index(name="ind_user_sign_up_route_user_id1_source_route1", columns={"user_id", "source_route" })})
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserSignUpRouteRepository")
 */
class UserSignUpRoute 
{

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
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="source_route", type="string" ,length=20)
     */
    private $sourceRoute;  # such as google, baidu

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_time", type="datetime")
     */
    private $createdAt; 

    public function __construct() {
        $this->createdAt = new \DateTime();
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

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserSignUpRoute
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
     * Set sourceRoute
     *
     * @param string $sourceRoute
     * @return UserSignUpRoute
     */
    public function setSourceRoute($sourceRoute)
    {
        $this->sourceRoute = $sourceRoute;

        return $this;
    }

    /**
     * Get sourceRoute
     *
     * @return string 
     */
    public function getSourceRoute()
    {
        return $this->sourceRoute;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserSignUpRoute
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
}
