<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarketActivity
 *
 * @ORM\Table(name="market_activity")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\MarketActivityRepository")
 */
class MarketActivity
{
    public $actImage;
    public function __construct()
    {
        $this->createTime = new \DateTime();
        $this->startTime = new \DateTime();
        $this->endTime = new \DateTime();
    }

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
     * @ORM\Column(name="aid", type="integer")
     */
    private $aid;

     /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", length=250, nullable=true)
     */
    private $businessName;
    
     /**
     * @var string
     *
     * @ORM\Column(name="activity_description", type="string", length=1000)
     */
    private $activityDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="category_id", type="string",length=250, nullable=true)
     */
    private $categoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="activity_url", type="string", length=1000, nullable=true)
     */
    private $activityUrl;

     /**
     * @var string
     *
     * @ORM\Column(name="activity_image", type="string", length=250, nullable=true)
     */
    private $activityImage;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true)
     */
    private $createTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=true)
     */
    private $deleteFlag;



    /**
     * editupload image to temp dir
     */
    public function editupload($upload_dir)
    {

        $fileNames = array('actImage');

        $types = array('jpg','jpeg','png','gif');

        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0777);
        }
        foreach ($fileNames as $key=>$fileName){
            $filename_upload = '';
            if (null === $this->$fileName) {
                unset($fileNames[$key]);
                continue ;
            }
            $field = 'activityImage';
            switch ($fileName){
                case 'actImage':$field = 'activityImage';break;
            }

            if($this->$fileName->getError()==1){
                return  '文件类型为jpg或png或gif';//类型不对
            }else{
                if(!in_array($this->$fileName->guessExtension(),$types)){
                    return  '文件类型为jpg或png或gif';//类型不对
                }else{
                    $size = getimagesize($this->$fileName);
                    if($size[0]=='470' && $size[1]=='200'){
                        $filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();

                        $this->$fileName->move($upload_dir, $filename_upload);

                        $this->$field = $upload_dir.$filename_upload;

                        $this->$fileName = null;
                    } else{
                        return   '图片像素为470X200';
                    }
                }
            }

        }
    }


    /**
     * upload image to temp dir
     */
    public function upload($upload_dir)
    {

        $fileNames = array('actImage');

        $types = array('jpg','jpeg','png','gif');

        if(!is_dir($upload_dir)){
            mkdir($upload_dir,0777);
        }
        foreach ($fileNames as $key=>$fileName){
            $filename_upload = '';
            if (null === $this->$fileName) {
                return  '图片为必填项';
                break ;
            }
            $field = 'activityImage';
            switch ($fileName){
                case 'actImage':$field = 'activityImage';break;
            }

            if($this->$fileName->getError()==1){
                return  '文件类型为jpg或png或gif';//类型不对
            }else{
                if(!in_array($this->$fileName->guessExtension(),$types)){
                    return  '文件类型为jpg或png或gif';//类型不对
                }else{
                    $size = getimagesize($this->$fileName);
                    if($size[0]=='470' && $size[1]=='200'){
                        $filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();

                        $this->$fileName->move($upload_dir, $filename_upload);

                        $this->$field = $upload_dir.$filename_upload;

                        $this->$fileName = null;
                    } else{
                        return   '图片像素为470X200';
                    }
                }
            }

        }
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
     * Set aid
     *
     * @param integer $aid
     * @return MarketActivity
     */
    public function setAid($aid)
    {
        $this->aid = $aid;

        return $this;
    }

    /**
     * Get aid
     *
     * @return integer
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     * @return MarketActivity
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set categoryId
     *
     * @param string $categoryId
     * @return MarketActivity
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }


    /**
     * Set activityUrl
     *
     * @param string $activityUrl
     * @return MarketActivity
     */
    public function setActivityUrl($activityUrl)
    {
        $this->activityUrl = $activityUrl;

        return $this;
    }

    /**
     * Get activityUrl
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return $this->activityUrl;
    }


    /**
     * Set activityImage
     *
     * @param string $activityImage
     * @return MarketActivity
     */
    public function setActivityImage($activityImage)
    {
        $this->activityImage = $activityImage;

        return $this;
    }

    /**
     * Get activityImage
     *
     * @return string
     */
    public function getActivityImage()
    {
        return $this->activityImage;
    }

    /**
     * Get activityDescription
     *
     * @return string 
     */
    public function getActivityDescription()
    {
        return $this->activityDescription;
    }


    /**
     * Set activityDescription
     *
     * @param string $activityDescription
     * @return MarketActivity
     */
    public function setActivityDescription($activityDescription)
    {
        $this->activityDescription = $activityDescription;

        return $this;
    }
    
    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return MarketActivity
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return MarketActivity
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return MarketActivity
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
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return MarketActivity
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


}
