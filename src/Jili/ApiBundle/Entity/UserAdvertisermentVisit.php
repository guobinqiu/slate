<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAdvertisermentVisit
 *
 * @ORM\Table(name="user_advertiserment_visit")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\UserAdvertisermentVisitRepository")
 */
class UserAdvertisermentVisit
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
    private $userId;

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
     * Set userId
     *
     * @param integer $userId
     * @return UserAdvertisermentVisit
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
     * Set visitDate
     *
     * @param string $visitDate
     * @return UserAdvertisermentVisit
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
