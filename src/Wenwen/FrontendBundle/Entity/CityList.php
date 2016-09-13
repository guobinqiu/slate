<?php

namespace Wenwen\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CityList
 *
 * @ORM\Table(name="cityList")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Wenwen\FrontendBundle\Repository\CityListRepository")
 */
class CityList
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
     * @var string
     *
     * @ORM\Column(name="cityName", type="string", length=50)
     */
    private $cityName;


    /**
     * @var integer
     *
     * @ORM\Column(name="provinceId", type="integer")
     */
    private $provinceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="integer")
     */
    private $cityId;


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
     * Set cityName
     *
     * @param string $cityName
     * @return CityList
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Get cityName
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }


    /**
     * Set provinceId
     *
     * @param integer $provinceId
     * @return CityList
     */
    public function setProvinceId($provinceId)
    {
        $this->provinceId = $provinceId;

        return $this;
    }

    /**
     * Get provinceId
     *
     * @return integer
     */
    public function getProvinceId()
    {
        return $this->provinceId;
    }

    public function getCityId()
    {
        return $this->cityId;
    }

    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
    }
}
