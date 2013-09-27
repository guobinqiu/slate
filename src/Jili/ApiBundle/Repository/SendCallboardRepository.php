<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class SendCallboardRepository extends EntityRepository
{
	public function getSendCb()
	{
		$query = $this->createQueryBuilder('scb');
		$query = $query->select('scb.id,scb.sendFrom,scb.sendTo,scb.title,scb.content,scb.createtime,scb.readFlag,scb.deleteFlag');
		$query = $query->Where('scb.deleteFlag = 0 ');
		$query = $query->orderBy('scb.createtime','DESC');
		$query =  $query->getQuery();
		return $query->getResult();
	}



	public function CountAllCallboard(){
		$query = $this->createQueryBuilder('scb');
		$query = $query->select('count(scb.id) as num');
		$query = $query->Where('scb.deleteFlag = 0 ');
		$query =  $query->getQuery();
		return $query->getResult();
	}


	public function CountIsReadCallboard($uid){
		$query = $this->createQueryBuilder('scb');
		$query = $query->select('count(scb.id) as num');
		$query = $query->leftJoin('JiliApiBundle:IsReadCallboard', 'ir', 'WITH', 'scb.id = ir.sendCbId');
		$query = $query->Where('scb.deleteFlag = 0 ');
		$query = $query->andWhere('ir.userId = :uid');
		$query = $query->setParameter('uid',$uid);
		$query =  $query->getQuery();
		return $query->getResult();
	}

	
}