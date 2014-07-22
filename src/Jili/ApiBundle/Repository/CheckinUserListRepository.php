<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class CheckinUserListRepository extends EntityRepository
{
    public function countUserList($uid,$date)
    {
        $query = $this->createQueryBuilder('cul');
        $query = $query->select('cul.id,cul.userId,cul.createTime,cul.clickDate,cul.openShopId');
        $query = $query->Where('cul.userId = :uid');
        $query = $query->andWhere('cul.clickDate = :cdate');
        $query = $query->setParameters(array('uid'=>$uid,'cdate'=>$date));
        $query = $query->getQuery();
        return count($query->getResult());
    }

    public function issetClickShop($uid,$date,$clickAdid)
    {
        $query = $this->createQueryBuilder('cul');
        $query = $query->select('cul.id,cul.userId,cul.createTime,cul.clickDate,cul.openShopId');
        $query = $query->Where('cul.userId = :uid');
        $query = $query->andWhere('cul.clickDate = :cdate');
        $query = $query->andWhere('cul.openShopId = :ckid');
        $query = $query->setParameters(array('uid'=>$uid,'cdate'=>$date,'ckid'=>$clickAdid));
        $query = $query->getQuery();
        return $query->getResult();
    }

}
