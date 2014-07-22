<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class CardRecordedMatchRepository extends EntityRepository
{
    public function cardByTime()
    {
        $query = $this->createQueryBuilder('crm');
        $query = $query->select('crm.id,crm.userId,crm.matchCount,crm.isProvideFlag,crm.createTime,u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'crm.userId = u.id');
        $query = $query->orderBy('crm.createTime','DESC');
        $query =  $query->getQuery();
        return $query->getResult();
    }

    public function userCardInfo($uid_flag)
    {
        $query = $this->createQueryBuilder('crm');
        $query = $query->select('crm.id,crm.userId,crm.matchCount,crm.isProvideFlag,crm.createTime,u.email,crr.rewardCount,crr.rewardPoint');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'crm.userId = u.id');
        $query = $query->innerJoin('JiliApiBundle:CardRecordedReward', 'crr', 'WITH', 'crm.id = crr.matchId');
        $query = $query->andWhere('u.id like :uid or u.email like :uid');
        $query = $query->orderBy('crm.createTime','DESC');
        $query = $query->setParameter('uid',$uid_flag);
        $query =  $query->getQuery();
        return $query->getResult();
    }


}
