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
	
	public function isFromWenwen($email)
	{
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.isFromWenwen = 1');
		$query = $query->setParameter('email',$email);
		$query = $query->getQuery();
		return $query->getResult();
	
	}
	
	public function isWenwenPwd($email)
	{
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.pwd');
		$query = $query->Where('u.email = :email');
		$query = $query->setParameter('email',$email);
		$query = $query->getQuery();
		$result = $query->getResult();
		return $result[0]['pwd'];
	
	}
}