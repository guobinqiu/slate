<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class CardRecordedRemainRepository extends EntityRepository
{
	public function userCardRemain($uid_flag)
	{
		$query = $this->createQueryBuilder('crr');
		$query = $query->select('crr.id,crr.remainCount');
		$query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'crr.userId = u.id');
		$query = $query->andWhere('u.id like :uid or u.email like :uid');
		$query = $query->setParameter('uid',$uid_flag);
		$query =  $query->getQuery();
		return $query->getResult();
	}
	
	
}