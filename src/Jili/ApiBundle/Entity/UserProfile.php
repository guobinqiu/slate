<?php

namespace Jili\ApiBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProfile
 *
 * @ORM\Entity
 * @ORM\Table(name="user_profile")
 */
class UserProfile
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var date
     *
     * @ORM\Column(name="birthday", type="string", length=50, nullable=true)
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $birthday;

    /**
     * @var integer
     *
     * @ORM\Column(name="sex", type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $sex;

    /**
     * @var text
     *
     * @ORM\Column(name="personalDes", type="text", nullable=true, options={"comment": "个性说明"})
     * @Assert\Length(max=512)
     */
    private $personalDes;

    /**
     * @var string
     *
     * @ORM\Column(name="fav_music", type="string", length=255, nullable=true, options={"comment": "喜欢的音乐"})
     * @Assert\Length(max=64)
     */
    private $favMusic;

    /**
     * @var string
     *
     * @ORM\Column(name="monthly_wish", type="string", length=255, nullable=true, options={"comment": "本月心愿"})
     * @Assert\Length(max=64)
     */
    private $monthlyWish;

    /**
     * @var integer
     *
     * @ORM\Column(name="province", type="integer", nullable=true)
     */
    private $province;

    /**
     * @var integer
     *
     * @ORM\Column(name="city", type="integer", nullable=true)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="income", type="integer", nullable=true)
     */
    private $income;

    /**
     * @var integer
     *
     * @ORM\Column(name="profession", type="integer", nullable=true, options={"comment": "职业"})
     */
    private $profession;

    /**
     * @var integer
     *
     * @ORM\Column(name="industry_code", type="integer", nullable=true, options={"comment": "行业"})
     */
    private $industryCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="work_section_code", type="integer", nullable=true, options={"comment": "部门"})
     */
    private $workSectionCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="education", type="integer", nullable=true, options={"comment": "学历"})
     */
    private $education;

    /**
     * @var integer
     *
     * @ORM\Column(name="hobby", type="string", length=250, nullable=true, options={"comment": "爱好"})
     */
    private $hobby;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="userProfile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set birthday
     *
     * @param string $birthday
     * @return UserProfile
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set sex
     *
     * @param integer $sex
     * @return UserProfile
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return integer
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set personalDes
     *
     * @param string $personalDes
     * @return UserProfile
     */
    public function setPersonalDes($personalDes)
    {
        $this->personalDes = $personalDes;

        return $this;
    }

    /**
     * Get personalDes
     *
     * @return string
     */
    public function getPersonalDes()
    {
        return $this->personalDes;
    }

    /**
     * Set favMusic
     *
     * @param string $favMusic
     * @return UserProfile
     */
    public function setFavMusic($favMusic)
    {
        $this->favMusic = $favMusic;

        return $this;
    }

    /**
     * Get favMusic
     *
     * @return string
     */
    public function getFavMusic()
    {
        return $this->favMusic;
    }

    /**
     * Set monthlyWish
     *
     * @param string $monthlyWish
     * @return UserProfile
     */
    public function setMonthlyWish($monthlyWish)
    {
        $this->monthlyWish = $monthlyWish;

        return $this;
    }

    /**
     * Get monthlyWish
     *
     * @return string
     */
    public function getMonthlyWish()
    {
        return $this->monthlyWish;
    }

    /**
     * Set province
     *
     * @param integer $province
     * @return UserProfile
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return integer
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set city
     *
     * @param integer $city
     * @return UserProfile
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return integer
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set income
     *
     * @param integer $income
     * @return UserProfile
     */
    public function setIncome($income)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income
     *
     * @return integer
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set profession
     *
     * @param integer $profession
     * @return UserProfile
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get profession
     *
     * @return integer
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set industryCode
     *
     * @param integer $industryCode
     * @return UserProfile
     */
    public function setIndustryCode($industryCode)
    {
        $this->industryCode = $industryCode;

        return $this;
    }

    /**
     * Get industryCode
     *
     * @return integer
     */
    public function getIndustryCode()
    {
        return $this->industryCode;
    }

    /**
     * Set workSectionCode
     *
     * @param integer $workSectionCode
     * @return UserProfile
     */
    public function setWorkSectionCode($workSectionCode)
    {
        $this->workSectionCode = $workSectionCode;

        return $this;
    }

    /**
     * Get workSectionCode
     *
     * @return integer
     */
    public function getWorkSectionCode()
    {
        return $this->workSectionCode;
    }

    /**
     * Set education
     *
     * @param integer $education
     * @return UserProfile
     */
    public function setEducation($education)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return integer
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set hobby
     *
     * @param string $hobby
     * @return UserProfile
     */
    public function setHobby($hobby)
    {
        $this->hobby = implode(',', $hobby);
        return $this;
    }

    /**
     * Get hobby
     *
     * @return array
     */
    public function getHobby()
    {
        return explode(',', $this->hobby);
    }

    /**
     * Set user
     *
     * @return UserProfile
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return UserProfile
     */
    public function getUser()
    {
        return $this->user;
    }
}