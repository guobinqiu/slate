<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class ExchangeFromWenwenRepository extends EntityRepository
{
    public function exFromWen($start,$end)
    {
        if($start)
            $start_time = $start.' 00:00:00';
        if($end)
            $end_time = $end.' 23:59:59';
        $query = $this->createQueryBuilder('efw');
        $query = $query->select('efw.wenwenExchangeId,efw.email,efw.userWenwenCrossId,efw.paymentPoint,efw.status,efw.reason,efw.createTime');
        $query = $query->Where('1 = 1');
        if($start){
            $query = $query->andWhere('efw.createTime>=:start_time');
            $query = $query->setParameter('start_time',$start_time);
        }
        if($end){
            $query = $query->andWhere('efw.createTime<=:end_time');
            $query = $query->setParameter('end_time',$end_time);
        }
        $query = $query->orderBy('efw.createTime','DESC');
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function eFrWenByIdMaxTen($uid)
    {
        $query = $this->createQueryBuilder('efw');
        $query = $query->select('efw.wenwenExchangeId,efw.email,efw.paymentPoint,efw.status,efw.reason,efw.createTime');
        $query = $query->Where('efw.userId = :uid');
        $query = $query->orderBy('efw.createTime','DESC');
        $query = $query->setParameter('uid',$uid);
        $query = $query->setFirstResult(0);
        $query = $query->setMaxResults(10);
        $query = $query->getQuery();
        return $query->getResult();

    }

    public function eFrWenByIdCount($uid)
    {
        $query = $this->createQueryBuilder('efw');
        $query = $query->select('COUNT(efw.id)');
        $query = $query->Where('efw.userId = :uid');
        $query = $query->setParameter('uid',$uid);
        $query = $query->getQuery();
        return $query->getSingleScalarResult();
    }

    public function eFrWenById($uid, $page = 1, $page_size=10)
    {
        $query = $this->createQueryBuilder('efw');
        $query = $query->select('efw.wenwenExchangeId,efw.email,efw.paymentPoint,efw.status,efw.reason,efw.createTime');
        $query = $query->Where('efw.userId = :uid');
        $query = $query->orderBy('efw.createTime','DESC');
        $query = $query->setParameter('uid',$uid);

        if ((int) $page < 1) {
            $page = 1;
        }
        $query = $query->setFirstResult($page_size * ($page - 1));
        $query = $query->setMaxResults($page_size);

        $query = $query->getQuery();
        return $query->getResult();

    }


}
