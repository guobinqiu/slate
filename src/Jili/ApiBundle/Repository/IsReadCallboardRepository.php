<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class IsReadCallboardRepository extends EntityRepository
{
	public function isreadInfo($sendid,$id)
	{
		$query = $this->createQueryBuilder('ir');
		$query = $query->select('ir.id');
		$query = $query->Where('ir.sendCbId = :sendid');
		$query = $query->andWhere('ir.userId = :userId');
		$query = $query->setParameters(array('sendid'=>$sendid,'userId'=>$id));
		$query =  $query->getQuery();
		return $query->getResult();
	}

	public function getUserIsRead($uid){
		$query = $this->createQueryBuilder('ir');
		$query = $query->select('ir.sendCbId,ir.userId');
		$query = $query->Where('ir.userId = :uid');
		$query = $query->setParameter('uid',$uid);
		$query =  $query->getQuery();
		return $query->getResult();
	}
	
	
}