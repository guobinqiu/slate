<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserGameVisit
 *
 * @ORM\Table(name="user_game_visit")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserGameVisitRepository")
 */
class UserGameVisit
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

     /**
     * @var string
     *
     * @ORM\Column(name="visit_date", type="string", length=20)
     */
    private $visitDate;


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
     * Set userid
     *
     * @param integer $userid
     * @return UserGameVisit
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
        return $this->userid;
    }


    /**
     * Set visitDate
     *
     * @param string $visitDate
     * @return UserGameVisit
     */
    public function setVisitDate($visitDate)
    {
        $this->visitDate = $visitDate;

        return $this;
    }

    /**
     * Get visitDate
     *
     * @return string
     */
    public function getVisitDate()
    {
        return $this->visitDate;
    }



}
