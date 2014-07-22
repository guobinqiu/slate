<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class BusinessActivityRepository extends EntityRepository
{
    public function nowMall()
    {
        $date = date('Y-m-d H:i:s');
        $query = $this->createQueryBuilder('ba');
        $query = $query->select('ba.aid,a.imageurl,a.title');
        $query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ba.aid = a.id');
        $query = $query->Where('ba.deleteFlag is null');
        $query = $query->andWhere('ba.startTime <= :startTime');
        $query = $query->andWhere('ba.endTime >= :endTime');
        $query = $query->groupBy('ba.aid');
        $query = $query->setParameters(array('startTime'=>$date,'endTime'=>$date));
        $query =  $query->getQuery();
        return $query->getResult();
    }

    public function nowCate()
    {
        $date = date('Y-m-d H:i:s');
        $query = $this->createQueryBuilder('ba');
        $query = $query->select('ba.categoryId');
        $query = $query->Where('ba.deleteFlag is null');
        $query = $query->andWhere('ba.startTime <= :startTime');
        $query = $query->andWhere('ba.endTime >= :endTime');
        $query = $query->groupBy('ba.categoryId');
        $query = $query->setParameters(array('startTime'=>$date,'endTime'=>$date));
        $query =  $query->getQuery();
        return $query->getResult();
    }


    public function getAllBusinessList()
    {
        $query = $this->createQueryBuilder('ba');
        $query = $query->select('ba.id,ba.aid,ba.businessName,ba.categoryId,ba.activityUrl,ba.activityImage,ba.startTime,ba.endTime');
        $query = $query->Where('ba.deleteFlag is null');
        $query = $query->orderBy('ba.id','DESC');
        $query =  $query->getQuery();
        return $query->getResult();

    }

    public function nowActivity($aid=null)
    {
        $query = $this->createQueryBuilder('ba');
        $query = $query->select('ba.id,ba.aid,ba.businessName,ba.categoryId,ba.activityUrl,ba.activityImage,ba.startTime,ba.endTime,a.imageurl,a.title');
        $query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ba.aid = a.id');
        $query = $query->Where('ba.deleteFlag is null');
        if($aid){
            $query = $query->andWhere('ba.aid = :aid');
            $query = $query->setParameter('aid',$aid);
        }
        $query = $query->orderBy('ba.id','DESC');
        $query =  $query->getQuery();
        return $query->getResult();
    }

}
