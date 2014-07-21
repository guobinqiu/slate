<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class CheckinClickListRepository extends EntityRepository
{
    public function checkIsStatus()
    {
        $query = $this->createQueryBuilder('ccl');
        $query = $query->select('ccl.userId,ccl.clickDate,ccl.createTime,ccl.status,ccl.openShopTimes');
        $query = $query->Where('ccl.status = 1');
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function issetUserInfo($uid,$date)
    {
        $query = $this->createQueryBuilder('ccl');
        $query = $query->select('ccl.id,ccl.userId,ccl.clickDate,ccl.createTime,ccl.status,ccl.openShopTimes');
        $query = $query->Where('ccl.userId = :uid');
        $query = $query->andWhere('ccl.clickDate = :cdate');
        $query = $query->setParameters(array('uid'=>$uid,'cdate'=>$date));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function checkStatus($uid,$date)
    {
        $query = $this->createQueryBuilder('ccl');
        $query = $query->select('ccl.userId,ccl.clickDate,ccl.createTime,ccl.status,ccl.openShopTimes');
        $query = $query->Where('ccl.status = 1');
        $query = $query->andWhere('ccl.userId = :uid');
        $query = $query->andWhere('ccl.clickDate = :cdate');
        $query = $query->setParameters(array('uid'=>$uid,'cdate'=>$date));
        $query = $query->getQuery();
        return $query->getResult();
    }

}
