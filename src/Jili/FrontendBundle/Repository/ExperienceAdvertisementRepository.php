<?php
namespace Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class ExperienceAdvertisementRepository extends EntityRepository {

    public function getAdvertisement($limit = null) {
        $query = $this->createQueryBuilder('ea');
        $query = $query->select('ea.missionHall,ea.point,ea.missionImgUrl,ea.missionTitle');
        if (!is_null($limit)) {
            $query = $query->setFirstResult(0);
            $query = $query->setMaxResults($limit);
        }
        $query = $query->getQuery();
        return $query->getResult();
    }
    
    public function getAdvertisementList($limit = null) {
        $query = $this->createQueryBuilder('ea');
        $query = $query->select('ea');
        $query = $query->Where('ea.deleteFlag = 0');
        if (!is_null($limit)) {
            $query = $query->setFirstResult(0);
            $query = $query->setMaxResults($limit);
        }
        $query = $query->getQuery();
        return $query->getResult();
    }
    
}