<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserRepository extends EntityRepository
{
	public function getUserList($id)
	{
		$query = $this->createQueryBuilder('u');
	
		$query = $query->select('u.id,u.nick,u.email,sp.code');
		$query = $query->innerJoin('JiliApiBundle:setPasswordCode', 'sp', 'WITH', 'u.id = sp.userId');
	
		$query = $query->Where('u.id = :id');
		$query = $query->setParameter('id',$id);
		$query =  $query->getQuery();
		return $query->getResult();
		
	}
}