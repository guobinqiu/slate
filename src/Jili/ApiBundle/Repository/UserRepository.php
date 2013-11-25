<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserRepository extends EntityRepository
{
	public function userCount(){
		$query = $this->createQueryBuilder('u');
		$query = $query->select('count(u.id) as num');
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getUserCount($start,$end){
		if($start)
			$start_time = $start.' 00:00:00';
		if($end)
			$end_time = $end.' 23:59:59';
		$query = $this->createQueryBuilder('u');
		$query = $query->select('count(u.id) as num');
		if($start && $end){
			$query = $query->Where('u.registerDate>=:start_time');
			$query = $query->andWhere('u.registerDate<=:end_time');
			$query = $query->setParameters(array('start_time'=>$start_time,'end_time'=>$end_time));
		}else{
			if($start){
				$query = $query->Where('u.registerDate>=:start_time');
				$query = $query->setParameter('start_time',$start_time);
			}else{
				if($end){
					$query = $query->Where('u.registerDate<=:end_time');
					$query = $query->setParameter('end_time',$end_time);
				}	
			}
		}
		$query = $query->getQuery();
		return $query->getResult();
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

	public function getSearch($email){
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
		$query = $query->Where('u.email = :email');
		$query = $query->setParameter('email',$email);
		$query = $query->getQuery();
		return $query->getResult();
	}


	public function getMultiple($times){
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
		$query = $query->Where('u.rewardMultiple > :times');
		$query = $query->setParameter('times',$times);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function pointFail($type){
		$sqlpoint = "(select user_id,create_time from point_history00 union select user_id,create_time from point_history01 union select user_id,create_time from point_history02 union select user_id,create_time from point_history03 union select user_id,create_time from point_history04 union select user_id,create_time from point_history05 union select user_id,create_time from point_history06 union select user_id,create_time from point_history07 union select user_id,create_time from point_history08 union select user_id,create_time from point_history09) b";
		
		$sqltask = "(select user_id,status,date from task_history00 union select user_id,status,date from task_history01 union select user_id,status,date from task_history02 union select user_id,status,date from task_history03 union select user_id,status,date from task_history04 union select user_id,status,date from task_history05 union select user_id,status,date from task_history06 union select user_id,status,date from task_history07 union select user_id,status,date from task_history08 union select user_id,status,date from task_history09) c";

		$RegBefore = "b.user_id is null and TO_DAYS( now( ) ) - TO_DAYS( e.register_date ) >= $type and id not in (select id from user a left join ".$sqlpoint." on a.id = b.user_id left join ".$sqltask." on a.id = c.user_id where b.user_id is null and TO_DAYS( now( ) ) - TO_DAYS( a.register_date ) >= $type and TO_DAYS( c.date ) - TO_DAYS( a.register_date ) < $type  and c.status = 2)";

		$Regafter = "b.user_id is null and id not in (select id from user a left join ".$sqlpoint." on a.id = b.user_id left join ".$sqltask." on a.id = c.user_id where b.user_id is null and TO_DAYS( now() ) - TO_DAYS( c.date ) < $type  and c.status = 2)";
		$sql = "select e.id,e.email,e.nick,e.register_date from user e left join ".$sqlpoint." on e.id = b.user_id where
		       case when TO_DAYS( now( ) ) - TO_DAYS( e.register_date) < $type then ".$RegBefore." 
		       else ".$Regafter." end";

		return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();

	}
}