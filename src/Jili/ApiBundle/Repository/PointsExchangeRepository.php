<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\PointsExchange;


class PointsExchangeRepository extends EntityRepository
{
    /**
     *  $uid之外用户的支付宝支付记录
     */
    public function existTargetAcc($targetAcc,$uid)
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.userId,p.targetAccount');
        $query = $query->Where('p.targetAccount = :targetAcc');
        $query = $query->andWhere('p.userId <> :uid');
        $query = $query->orderBy('p.id','DESC');
        $query = $query->groupBy('p.userId');
        $query = $query->setParameters(array('targetAcc'=>$targetAcc,'uid'=>$uid));
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     *  $uid之外用户的密码相同的记录
     */
    public function existPwd($pwd,$uid)
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.userId');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
        $query = $query->Where('u.pwd = :pwd');
        $query = $query->andWhere('u.id <> :uid');
        $query = $query->setParameters(array('pwd'=>$pwd,'uid'=>$uid));
        $query =  $query->getQuery();
        return $query->getResult();
    }


    /**
     *  其它用户uid使用$ip 兑换过
     */
    public function existIp($ip,$uid)
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.userId,p.ip');
        $query = $query->Where('p.ip = :ip');
        $query = $query->andWhere('p.userId <> :uid');
        $query = $query->orderBy('p.id','DESC');
        $query = $query->groupBy('p.userId');
        $query = $query->setParameters(array('ip'=>$ip,'uid'=>$uid));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function exList()
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.userId,u.nick,u.wenwenUser,p.targetPoint,p.exchangeDate,p.finishDate,p.status,pt.type');
        $query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pt', 'WITH', 'p.type = pt.id');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
        $query = $query->Where('p.status=1');
        $query = $query->orderBy('p.id','DESC');
        $query = $query->setFirstResult(0);
        $query = $query->setMaxResults(10);
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function getTargetAccount($uid,$type)
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.targetAccount,p.realName');
        $query = $query->Where('p.userId = :uid');
        $query = $query->andWhere('p.type = :type');
        $query = $query->orderBy('p.id','DESC');
        $query = $query->setFirstResult(0);
        $query = $query->setMaxResults(1);
        $query = $query->setParameters(array('uid'=>$uid,'type'=>$type));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function exchangeInfo($start,$end)
    {
        if($start)
            $start_time = $start.' 00:00:00';
        if($end)
            $end_time = $end.' 23:59:59';
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.userId,u.email,p.targetAccount,p.targetPoint,p.exchangeDate,p.finishDate,p.status,pt.type');
        $query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pt', 'WITH', 'p.type = pt.id');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
        $query = $query->Where('1 = 1');
        if($start){
            $query = $query->andWhere('p.exchangeDate>=:start_time');
            $query = $query->setParameter('start_time',$start_time);
        }
        if($end){
            $query = $query->andWhere('p.exchangeDate<=:end_time');
            $query = $query->setParameter('end_time',$end_time);
        }
        $query = $query->getQuery();
        return $query->getResult();
    }


    public function getExDateInfo($start,$end,$type)
    {
        if($start)
            $start_time = $start.' 00:00:00';
        if($end)
            $end_time = $end.' 23:59:59';
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.userId,u.email,p.targetAccount,p.targetPoint,p.realName,p.type as exType,p.exchangeItemNumber,p.exchangeDate,p.finishDate,p.status,pt.type');
        $query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pt', 'WITH', 'p.type = pt.id');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
        $query = $query->Where('1 = 1');
        if($start){
            $query = $query->andWhere('p.exchangeDate>=:start_time');
            $query = $query->setParameter('start_time',$start_time);
        }
        if($end){
            $query = $query->andWhere('p.exchangeDate<=:end_time');
            $query = $query->setParameter('end_time',$end_time);
        }
        if($type){
            $query = $query->andWhere('p.type = :type');
            $query = $query->setParameter('type',$type);
        }
        $query = $query->orderBy('p.exchangeDate', 'DESC');
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function existUserExchange($id)
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.targetAccount,p.userId,p.sourcePoint,p.targetPoint,p.exchangeDate,p.finishDate,p.status');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
        $query = $query->Where('p.userId = :id');
        $query = $query->setParameter('id',$id);
        $query = $query->getQuery();
        return $query->getResult();
    }
    public function existOneExchange($uid,$type)
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.targetAccount,p.userId');
        $query = $query->Where('p.userId = :uid');
        $query = $query->andWhere('p.type = :type');
        $query = $query->orderBy('p.id','DESC');
        $query = $query->groupBy('p.userId');
        $query = $query->setParameters(array('uid'=>$uid,'type'=>$type));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function getExchangeStatus($id)
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.targetAccount,p.userId,p.sourcePoint,p.targetPoint,p.exchangeDate,p.finishDate,p.status');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
        $query = $query->Where('p.userId = :id');
        $query = $query->andWhere('p.status = 1');
        $query = $query->setParameter('id',$id);
        $query = $query->getQuery();
        return $query->getResult();
    }
    public function getUserExchange($id,$option=array())
    {
        $query = $this->createQueryBuilder('p');
        $query = $query->select('p.id,p.targetAccount,p.userId,p.type as exType,p.sourcePoint,p.targetPoint,p.exchangeItemNumber,p.exchangeDate,p.finishDate,p.status,pe.type');
        $query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pe', 'WITH', 'p.type = pe.id');
        $query = $query->Where('p.userId = :id');
        if($option['daytype']){
            $query = $query->andWhere('p.type = :type');
            $query = $query->setParameter('type',$option['daytype']);

        }
        $query = $query->setParameter('id',$id);
        $query = $query->orderBy('p.exchangeDate', 'DESC');
        if($option['offset'] && $option['limit']){
            $query = $query->setFirstResult(0);
            $query = $query->setMaxResults(10);
        }
        $query = $query->getQuery();
        return $query->getResult();

    }
    /**
     *
     * @return count of rows in JiliApiBundle:PointsExchange
     */
    public function getCount(array $wheres)
    {

        $qb = $this->createQueryBuilder('p');
        $qb->select( $qb->expr()->count('p.id'));

        $qb->where( '1=1');
        if(isset($wheres['start_date']) && strlen($wheres['start_date']) > 0) {
            $qb = $qb->andWhere('p.exchangeDate>=:start_at');
            $qb = $qb->setParameter('start_at',$wheres['start_date'] .' 00:00:00');
        }

        if(isset($wheres['end_date']) && strlen($wheres['end_date']) > 0 ) {
            $qb = $qb->andWhere('p.exchangeDate<=:end_at');
            $qb = $qb->setParameter('end_at',$wheres['end_date'] .' 23:59:59');
        }


        if(isset($wheres['type']) && $wheres['type'] > 0 ){
            $qb = $qb->andWhere('p.type = :type');
            $qb = $qb->setParameter('type', $wheres['type']);
        }

        $query = $qb->getQuery() ;
        $count = $query->getSingleScalarResult();
        return (int) $count;
    }

    /**
     *
     * @param array $params = array ( 'page' , 'count' )
     * @param array $wheres = array ( 'page' , 'count' )
     * @return current page
     *
     */
    public function getCurrent( array $params, $wheres = array() )
    {
        extract($params);

        $offset =  ($page -1)* $count ;
        $limit = $count;
// u.email,,pt.type
        $qb = $this->createQueryBuilder('p')
            ->select('p.id,p.userId,p.targetAccount,p.targetPoint,p.realName,p.type as exType,p.exchangeItemNumber,p.exchangeDate,p.finishDate,p.status')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );

        $qb->where( '1=1');
        if(isset($wheres['start_date']) && strlen($wheres['start_date']) > 0) {
            $qb = $qb->andWhere('p.exchangeDate>=:start_at');
            $qb = $qb->setParameter('start_at',$wheres['start_date'] .' 00:00:00');
        }

        if(isset($wheres['end_date']) && strlen($wheres['end_date']) > 0 ) {
            $qb = $qb->andWhere('p.exchangeDate<=:end_at');
            $qb = $qb->setParameter('end_at',$wheres['end_date'] .' 23:59:59');
        }

        if(isset($wheres['type']) && $wheres['type'] > 0 ){
            $qb = $qb->andWhere('p.type = :type');
            $qb = $qb->setParameter('type',$wheres['type']);
        }

        $qb = $qb->orderBy('p.exchangeDate', 'DESC');

        $q = $qb->getQuery();

        return $q->getResult();
    }

    public function insert($params = array ()) {
        $em = $this->getEntityManager();
        $pointschange  = new PointsExchange();
        $pointschange->setUserId($params['user_id']);
        $pointschange->setType($params['type']);
        $pointschange->setSourcePoint($params['source_point']);
        $pointschange->setTargetPoint($params['target_point']);
        $pointschange->setTargetAccount($params['target_account']);
        $pointschange->setExchangeItemNumber($params['exchange_item_number']);
        $pointschange->setIp($params['ip']);
        $em->persist($pointschange);
        $em->flush();
        return $pointschange;
    }
}
