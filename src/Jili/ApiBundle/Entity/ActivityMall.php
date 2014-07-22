<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityMall
 *
 * @ORM\Table(name="activity_mall")
 * @ORM\Entity
 */
class ActivityMall
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

     /**
     * @var string
     *
     * @ORM\Column(name="mall_name", type="string" ,length=250)
     */
    private $mallName;


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
     * Set mallName
     *
     * @param string $mallName
     * @return ActivityMall
     */
    public function setMallName($mallName)
    {
        $this->mallName = $mallName;

        return $this;
    }

    /**
     * Get mallName
     *
     * @return string
     */
    public function getMallName()
    {
        return $this->mallName;
    }



}
