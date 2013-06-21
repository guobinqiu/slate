<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdBanner
 *
 * @ORM\Table(name="ad_banner")
 * @ORM\Entity()
 */
class AdBanner
{
	public $attachment;
	public function __construct() {
		$this->createTime = new \DateTime();
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
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var string
     *
     * @ORM\Column(name="icon_image", type="string", length=250)
     */
    private $iconImage;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="ad_url", type="string", length=250)
     */
    private $adUrl;

    
    
    /**
     * upload image to temp dir
     */
    public function upload($upload_dir)
    {
    	 
    	$fileNames = array('attachment');
    	 
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
    		$field = 'iconImage';
    		switch ($fileName){
    		    case 'attachment':$field = 'iconImage';break;
    		}
    		 
    		if($this->$fileName->getError()==1){
    			return  '文件类型为jpg或png或gif';//类型不对
    		}else{
    			if(!in_array($this->$fileName->guessExtension(),$types)){
    				return  '文件类型为jpg或png或gif';//类型不对
    			}else{
    				$size = getimagesize($this->$fileName);
    				if($size[0]=='722' && $size[1]=='250'){
    					$filename_upload = time().'_'.rand(1000,9999).'.'.$this->$fileName->guessExtension();
    						
    					$this->$fileName->move($upload_dir, $filename_upload);
    						
    					$this->$field = $upload_dir.$filename_upload;
    						
    					$this->$fileName = null;
    				}
    				else{
    					return   '图片像素为722X250';
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
     * Set iconImage
     *
     * @param string $iconImage
     * @return AdBanner
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
     * Set adUrl
     *
     * @param string $adUrl
     * @return AdBanner
     */
    public function setAdUrl($adUrl)
    {
    	$this->adUrl = $adUrl;
    
    	return $this;
    }
    
    /**
     * Get adUrl
     *
     * @return string
     */
    public function getAdUrl()
    {
    	return $this->adUrl;
    }
    
    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return AdBanner
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
    
}
