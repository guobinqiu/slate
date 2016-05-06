<?php
namespace Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\FrontendBundle\Entity\ExperienceAdvertisement;
use Symfony\Component\Filesystem\Filesystem;

class ExperienceAdvertisementRepository extends EntityRepository 
{
    const FILE_UPLOAD_ERROR_CODE = 1;
    
    public function getAdvertisement($limit = null) {
        $query = $this->createQueryBuilder('ea');
        $query = $query->select('ea.missionHall,ea.point,ea.missionImgUrl,ea.missionTitle');
        $query = $query->Where('ea.deleteFlag IS NULL OR ea.deleteFlag =:deleteFlag');
        if (!is_null($limit)) {
            $query = $query->setFirstResult(0);
            $query = $query->setMaxResults($limit);
        }
        $query = $query->setParameter('deleteFlag',0);
        $query = $query->getQuery();
        return $query->getResult();
    }
    
    public function getAdvertisementList($limit = null) {
        $query = $this->createQueryBuilder('ea');
        $query = $query->select('ea');
        $query = $query->Where('ea.deleteFlag IS NULL OR ea.deleteFlag =:deleteFlag');
        if (!is_null($limit)) {
            $query = $query->setFirstResult(0);
            $query = $query->setMaxResults($limit);
        }
        $query = $query->setParameter('deleteFlag',0);
        $query = $query->getQuery();
        return $query->getResult();
    }
    
    /**
     * upload image
     */
    public function upload($upload_dir, $ea, $upload_dir_old = null, $old_img = null)
    {
        //$ea = new ExperienceAdvertisement();
        $fileNames = array('missionImgUrl');
        $types = array('jpg','jpeg','png','gif');

        \Jili\ApiBundle\Utility\FileUtil::mkdir($upload_dir);

        foreach ($fileNames as $key=>$fileName){
            $filename_upload = '';
            if (null === $ea->$fileName) {
                return  '图片为必填项';
            }
            if($ea->$fileName->getError()==self::FILE_UPLOAD_ERROR_CODE){
                return  '文件类型为jpg或png或gif';//类型不对
            }
            if(!in_array($ea->$fileName->guessExtension(),$types)){
                return  '文件类型为jpg或png或gif';//类型不对
            }
            $size = getimagesize($ea->$fileName);
            if(($ea->getMissionHall()==1 && $size[0]=='165' && $size[1]=='112') || ($ea->getMissionHall()==2 && $size[0]=='165' && $size[1]=='112')){
                $filename_upload = time().'_'.rand(1000,9999).'.'.$ea->$fileName->guessExtension();
                $ea->$fileName->move($upload_dir, $filename_upload);
                $ea->$fileName = $upload_dir.$filename_upload;
                if($upload_dir_old && $old_img){
                    $fs = new Filesystem();
                    $fs->rename($old_img, $upload_dir_old.str_replace($upload_dir, '', $old_img));
                }
            } else{
                return   '图片像素不正确';
            }
        }
    }
}
