<?php
namespace Jili\FrontendBundle\Entity;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * ExperienceAdvertisement
 *
 * @ORM\Table(name="experience_advertisement")
 * @ORM\Entity(repositoryClass="Jili\FrontendBundle\Repository\ExperienceAdvertisementRepository")
 */
class ExperienceAdvertisement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="mission_hall", type="integer", options={"comment": "1 任务大厅1, 2 任务大厅2"})
     */
    private $missionHall;

    /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer", nullable=true, options={"comment": "米粒"})
     */
    private $point;

    /**
     * @var string
     *
     * @ORM\Column(name="mission_img_url", type="string", length=250, nullable=true, options={"comment": "任务图片链接"})
     */
    public $missionImgUrl;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mission_title", type="string", length=250, nullable=true, options={"comment": "任务标题"})
     */
    private $missionTitle;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=true)
     */
    private $deleteFlag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true)
     */
    private $createTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_time", type="datetime", nullable=true)
     */
    private $updateTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set missionHall
     *
     * @param integer $missionHall
     * @return ExperienceAdvertisement
     */
    public function setMissionHall($missionHall)
    {
        $this->missionHall = $missionHall;

        return $this;
    }

    /**
     * Get missionHall
     *
     * @return integer
     */
    public function getMissionHall()
    {
        return $this->missionHall;
    }

    /**
     * Set point
     *
     * @param integer $point
     * @return ExperienceAdvertisement
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set missionImgUrl
     *
     * @param string $missionImgUrl
     * @return ExperienceAdvertisement
     */
    public function setMissionImgUrl($missionImgUrl)
    {
        $this->missionImgUrl = $missionImgUrl;
        return $this;
    }

    /**
     * Get missionImgUrl
     *
     * @return string
     */
    public function getMissionImgUrl()
    {
        if ($this->missionImgUrl) {
           return new File($this->missionImgUrl);
        }
    }

    
    /**
     * Set missionTitle
     *
     * @param string $missionTitle
     * @return ExperienceAdvertisement
     */
    public function setMissionTitle($missionTitle)
    {
        $this->missionTitle = $missionTitle;

        return $this;
    }

    /**
     * Get missionTitle
     *
     * @return string
     */
    public function getMissionTitle()
    {
        return $this->missionTitle;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return ExperienceAdvertisement
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;

        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return integer
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return ExperienceAdvertisement
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     * @return ExperienceAdvertisement
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
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
