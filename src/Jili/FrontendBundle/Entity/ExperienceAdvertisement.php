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
     * @ORM\Column(name="mission_hall", type="integer", nullable=false)
     */
    private $missionHall;

    /**
     * @var integer
     *
     * @ORM\Column(name="point", type="integer", nullable=true)
     */
    private $point;

    /**
     * @var string
     *
     * @ORM\Column(name="mission_img_url", type="string", length=250, nullable=true)
     */
    private $missionImgUrl;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mission_title", type="string", length=250, nullable=true)
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
        if ($this->missionImgUrl)
        return new File($this->missionImgUrl);
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
    

    /**
     * upload image
     */
    public function upload($upload_dir,$missionHall)
    {
        $fileNames = array('missionImgUrl');
        $types = array('jpg','jpeg','png','gif');
        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0777);
        }
        foreach ($fileNames as $key=>$fileName){
            $filename_upload = '';
            if (null === $this->$fileName) {
                return  '图片为必填项';
            }
            if($this->$fileName->getError()==1){
                return  '文件类型为jpg或png或gif';//类型不对
            }
            if(!in_array($this->$fileName->guessExtension(),$types)){
                return  '文件类型为jpg或png或gif';//类型不对
            }
            $size = getimagesize($this->$fileName);
            if(($missionHall==1 && $size[0]=='94' && $size[1]=='78') || ($missionHall==2 && $size[0]=='116' && $size[1]=='78')){
                $filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();
                $this->$fileName->move($upload_dir, $filename_upload);
                $this->$fileName = $upload_dir.$filename_upload;
            } else{
                return   '图片像素不正确';
            }
        }
    }

}