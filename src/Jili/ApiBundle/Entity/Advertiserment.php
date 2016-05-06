<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Advertiserment
 *
 * @ORM\Table(name="advertiserment")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdvertisermentRepository")
 */
class Advertiserment
{
    public $large;
    public $small;
    public function __construct()
    {
        $this->createdTime = new \DateTime();
        $this->updateTime = new \DateTime();
        $this->startTime = new \DateTime();
        $this->endTime = new \DateTime();
        $this->isExpired = 0;
        $this->rewardRate = 30;
        $this->deleteFlag = 0;
        $this->category = 0;

    }
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
     * @ORM\Column(name="type", type="string", length=250, nullable=true)
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45, nullable=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="action_id", type="integer", nullable=true, options={"comment": "emar  action_id"})
     */
    private $actionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_time", type="datetime", nullable=true)
     */
    private $createdTime;

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
     * @ORM\Column(name="update_time", type="datetime", nullable=true)
     */
    private $updateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="decription", type="string", length=1000, nullable=true)
     */
    private $decription;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="imageurl", type="string", length=250, nullable=true)
     */
    private $imageurl;


    /**
     * @var boolean
     *
     * @ORM\Column(name="is_expired", type="boolean", nullable=true, options={"default": 0, "comment": "imageurl response reports expired"})
     */
    private $isExpired;

    /**
     * @var string
     *
     * @ORM\Column(name="icon_image", type="string", length=250, nullable=true)
     */
    private $iconImage;

    /**
     * @var string
     *
     * @ORM\Column(name="list_image", type="string", length=250, nullable=true)
     */
    private $listImage;

    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_type", type="integer", nullable=true)
     */
    private $incentiveType;

    /**
     * @var integer
     *
     * @ORM\Column(name="incentive_rate", type="integer", nullable=true)
     */
    private $incentiveRate;

    /**
     * @var float
     *
     * @ORM\Column(name="reward_rate", type="float", nullable=true, options={"default": 30})
     */
    private $rewardRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="incentive", type="integer", nullable=true)
     */
    private $incentive;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;

    /**
     * @var integer
     *
     * @ORM\Column(name="category", type="integer", nullable=true, options={"default": 0})
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_flag", type="integer", nullable=true, options={"default": 0})
     */
    private $deleteFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="wenwen_user", type="string", length=100, nullable=true)
     */
    private $wenwenUser;
    /**
     * upload image to temp dir
     */
    public function upload($upload_dir)
    {

        $fileNames = array('large','small');
        $types = array('jpg','jpeg','png','gif');
        \Jili\ApiBundle\Utility\FileUtil::mkdir($upload_dir);
        foreach ($fileNames as $key=>$fileName){
            $filename_upload = '';
            if (null === $this->$fileName) {
//     			unset($fileNames[$key]);
                echo  '图片为必填项';
                continue ;
            }
//     		else{
                $field = 'iconImage';
                switch ($fileName){
                    case 'large':$field = 'iconImage';break;
                    case 'small':$field = 'listImage';break;
                }

                if($this->$fileName->getError()==1){
                    return  '文件类型为jpg或png或gif';//类型不对
                }else{
                    if(!in_array($this->$fileName->guessExtension(),$types)){
                        return  '文件类型为jpg或png或gif';//类型不对
                    }else{
                        $size = getimagesize($this->$fileName);
                        if($fileName=='large'){
                            if($size[0]!='300' && $size[1]!='250')
                                return   '图片像素为300X250';
                        }
                        if($fileName=='small'){
                            if($size[0]!='200' && $size[1]!='130')
                                return   '图片像素为200X130';
                        }
                        $filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();

                        $this->$fileName->move($upload_dir, $filename_upload);

                        $this->$field = $upload_dir.$filename_upload;

                        $this->$fileName = null;

                        // 	        		return '';

                    }
                }
//     		}

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
     * Set type
     *
     * @param string $type
     * @return Advertiserment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Advertiserment
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set actionId
     *
     * @param integer $actionId
     * @return Advertiserment
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;

        return $this;
    }

    /**
     * Get actionId
     *
     * @return integer
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Set createdTime
     *
     * @param \DateTime $createdTime
     * @return Advertiserment
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    /**
     * Get createdTime
     *
     * @return \DateTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Advertiserment
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
     * @return Advertiserment
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
     * Set updateTime
     *
     * @param \DateTime $updateTime
     * @return Advertiserment
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
     * Set decription
     *
     * @param string $decription
     * @return Advertiserment
     */
    public function setDecription($decription)
    {
        $this->decription = $decription;

        return $this;
    }

    /**
     * Get decription
     *
     * @return string
     */
    public function getDecription()
    {
        return $this->decription;
    }

    /**
     * Set content
     *
     * @param text $content
     * @return Advertiserment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set imageurl
     *
     * @param string $imageurl
     * @return Advertiserment
     */
    public function setImageurl($imageurl)
    {
        $this->imageurl = $imageurl;

        return $this;
    }

    /**
     * Get imageurl
     *
     * @return string
     */
    public function getImageurl()
    {
        return $this->imageurl;
    }

    /**
     * Set isExpired
     *
     * @param boolean $isExpired
     * @return Advertiserment
     */
    public function setIsExpired($isExpired)
    {
        $this->isExpired = $isExpired;

        return $this;
    }

    /**
     * Get isExpired
     *
     * @return boolean 
     */
    public function getIsExpired()
    {
        return $this->isExpired;
    }

    /**
     * Set iconImage
     *
     * @param string $iconImage
     * @return Advertiserment
     */
    public function setIconImage($iconImage)
    {
        $this->iconImage = $iconImage;

        return $this;
    }

    /**
     * Get iconImage
     *
     * @return string
     */
    public function getIconImage()
    {
        return $this->iconImage;
    }

    /**
     * Set listImage
     *
     * @param string $listImage
     * @return Advertiserment
     */
    public function setListImage($listImage)
    {
        $this->listImage = $listImage;

        return $this;
    }

    /**
     * Get listImage
     *
     * @return string
     */
    public function getListImage()
    {
        return $this->listImage;
    }

    /**
     * Set incentiveType
     *
     * @param integer $incentiveType
     * @return Advertiserment
     */
    public function setIncentiveType($incentiveType)
    {
        $this->incentiveType = $incentiveType;

        return $this;
    }

    /**
     * Get incentiveType
     *
     * @return integer
     */
    public function getIncentiveType()
    {
        return $this->incentiveType;
    }


    /**
     * Set incentiveRate
     *
     * @param integer $incentiveRate
     * @return Advertiserment
     */
    public function setIncentiveRate($incentiveRate)
    {
        $this->incentiveRate = $incentiveRate;

        return $this;
    }

    /**
     * Get incentiveRate
     *
     * @return integer
     */
    public function getIncentiveRate()
    {
        return $this->incentiveRate;
    }

     /**
     * Set rewardRate
     *
     * @param float $rewardRate
     * @return Advertiserment
     */
    public function setRewardRate($rewardRate)
    {
        $this->rewardRate = $rewardRate;

        return $this;
    }

    /**
     * Get rewardRate
     *
     * @return float
     */
    public function getRewardRate()
    {
        return $this->rewardRate;
    }



    /**
     * Set incentive
     *
     * @param integer $incentive
     * @return Advertiserment
     */
    public function setIncentive($incentive)
    {
        $this->incentive = $incentive;

        return $this;
    }

    /**
     * Get incentive
     *
     * @return integer
     */
    public function getIncentive()
    {
        return $this->incentive;
    }



    /**
     * Set info
     *
     * @param text $info
     * @return Advertiserment
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return text
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set category
     *
     * @param integer $category
     * @return Advertiserment
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return integer
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set deleteFlag
     *
     * @param integer $deleteFlag
     * @return Advertiserment
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
     * Set wenwenUser
     *
     * @param string $wenwenUser
     * @return Advertiserment
     */
    public function setWenwenUser($wenwenUser)
    {
        $this->wenwenUser = $wenwenUser;

        return $this;
    }

    /**
     * Get wenwenUser
     *
     * @return string 
     */
    public function getWenwenUser()
    {
        return $this->wenwenUser;
    }

    /**
     * @param interger $user_id the User id
     * @return string
     */
    public function getImageurlParsed($user_id)
    {
        $image_url = $this->getImageurl();
        if(strlen($image_url) <= 0 ){
            return $image_url;
        }

        $adw_info = explode('u=',$image_url);

        if( count($adw_info) !== 2) {
            return $image_url;
        }
        $new_url = trim($adw_info[0]).'u='.$user_id.trim($adw_info[1]).$this->getId();
        return $new_url;
    }

}
