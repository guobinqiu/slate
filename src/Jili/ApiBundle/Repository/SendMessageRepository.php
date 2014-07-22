<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class SendMessageRepository extends EntityRepository
{
    public function getSendMs()
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('sm.id,sm.sendFrom,sm.sendTo,sm.title,sm.content,sm.createtime,sm.readFlag,sm.deleteFlag,u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id ');
        $query = $query->Where('sm.deleteFlag = 0 ');
        $query = $query->orderBy('sm.createtime','DESC');
        $query =  $query->getQuery();
        return $query->getResult();
    }

    public function getUserSendMs($id)
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('sm.id,sm.sendFrom,sm.sendTo,sm.title,sm.content,sm.createtime,sm.readFlag,sm.deleteFlag,u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id ');
        $query = $query->Where('sm.id = :id');
        $query = $query->setParameter('id',$id);
        $query =  $query->getQuery();
        return $query->getResult();
    }

    public function getSendMsById($uid)
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('sm.id,sm.sendFrom,sm.sendTo,sm.title,sm.content,sm.createtime,sm.readFlag,sm.deleteFlag,u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id');
        $query = $query->Where('sm.sendTo = :uid');
        $query = $query->andWhere('sm.deleteFlag = 0 ');
        $query = $query->orderBy('sm.createtime','DESC');
        $query = $query->setParameter('uid',$uid);
        $query =  $query->getQuery();
        return $query->getResult();
    }


    public function CountSendMs($uid)
    {
        $query = $this->createQueryBuilder('sm');
        $query = $query->select('count(sm.id) as num');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'sm.sendTo = u.id');
        $query = $query->Where('sm.sendTo = :uid');
        $query = $query->andWhere('sm.readFlag = 0 ');
        $query = $query->andWhere('sm.deleteFlag = 0 ');
        $query = $query->setParameter('uid',$uid);
        $query =  $query->getQuery();
        return $query->getResult();
    }

}
