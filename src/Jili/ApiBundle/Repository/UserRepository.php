<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserRepository extends EntityRepository
{
	public function getUserCount($start,$end){
		if($start)
			$start_time = $start.' 00:00:00';
		if($end)
			$end_time = $end.' 23:59:59';
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick');

		if($start && $end){
			$query = $query->Where('u.registerDate>=:start_time');
			$query = $query->andWhere('u.registerDate<=:end_time');
			$query = $query->setParameters(array('start_time'=>$start_time,'end_time'=>$end_time));
		}
		if($start && !$end){
			$query = $query->Where('u.registerDate>=:start_time');
			$query = $query->setParameter('start_time',$start_time);
		}
		if(!$start && $end){
			$query = $query->Where('u.registerDate<=:end_time');
			$query = $query->setParameter('end_time',$end_time);
		}
		$query = $query->getQuery();
		return count($query->getResult());
	}
    
	public function findNick($email,$nick)
	{
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id');
		$query = $query->Where('u.nick = :nick');
		$query = $query->andWhere('u.email <> :email');
		$query = $query->setParameters(array('email'=>$email,'nick'=>$nick));
		$query =  $query->getQuery();
		return $query->getResult();
		
	}

	public function getWenwenUser($email)
	{
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.pwd is not null');
		$query = $query->setParameter('email',$email);
		$query =  $query->getQuery();
		return $query->getResult();
		
	}

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
	
	public function isPwd($email)
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