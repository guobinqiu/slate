<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserRepository extends EntityRepository {
	public function userCount() {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('count(u.id) as num');
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getUserCount($start, $end) {
		if ($start)
			$start_time = $start . ' 00:00:00';
		if ($end)
			$end_time = $end . ' 23:59:59';
		$query = $this->createQueryBuilder('u');
		$query = $query->select('count(u.id) as num');
		if ($start && $end) {
			$query = $query->Where('u.registerDate>=:start_time');
			$query = $query->andWhere('u.registerDate<=:end_time');
			$query = $query->setParameters(array (
				'start_time' => $start_time,
				'end_time' => $end_time
			));
		} else {
			if ($start) {
				$query = $query->Where('u.registerDate>=:start_time');
				$query = $query->setParameter('start_time', $start_time);
			} else {
				if ($end) {
					$query = $query->Where('u.registerDate<=:end_time');
					$query = $query->setParameter('end_time', $end_time);
				}
			}
		}
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function findNick($email, $nick) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id');
		$query = $query->Where('u.nick = :nick');
		$query = $query->andWhere('u.email <> :email');
		$query = $query->setParameters(array (
			'email' => $email,
			'nick' => $nick
		));
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function getWenwenUser($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.pwd is not null');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function getUserList($id) {
		$query = $this->createQueryBuilder('u');

		$query = $query->select('u.id,u.nick,u.email,sp.code');
		$query = $query->innerJoin('JiliApiBundle:setPasswordCode', 'sp', 'WITH', 'u.id = sp.userId');

		$query = $query->Where('u.id = :id');
		$query = $query->setParameter('id', $id);
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function isFromWenwen($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email');
		$query = $query->Where('u.email = :email');
		$query = $query->andWhere('u.isFromWenwen = 1');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function isPwd($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.pwd');
		$query = $query->Where('u.email = :email');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		$result = $query->getResult();
		return $result[0]['pwd'];

	}

	public function getSearch($email) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
		$query = $query->Where('u.email = :email');
		$query = $query->setParameter('email', $email);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getMultiple($times) {
		$query = $this->createQueryBuilder('u');
		$query = $query->select('u.id,u.nick,u.email,u.rewardMultiple');
		$query = $query->Where('u.rewardMultiple > :times');
		$query = $query->setParameter('times', $times);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function pointFail($type) {
		$daydate = date("Y-m-d H:i:s", strtotime(' -' . $type . ' day'));
		$sqlpoint = "(select b.user_id from (select user_id,reason,create_time from point_history00 union select user_id,reason,create_time from point_history01  union select user_id,reason,create_time from point_history02 union select user_id,reason,create_time from point_history03  union select user_id,reason,create_time from point_history04  union select user_id,reason,create_time from point_history05  union select user_id,reason,create_time from point_history06  union select user_id,reason,create_time from point_history07  union select user_id,reason,create_time from point_history08  union select user_id,reason,create_time from point_history09) b where create_time > '" . $daydate . "')";

		$sqltask = "(select c.user_id from (select user_id,status,date from task_history00 union select user_id,status,date from task_history01 union select user_id,status,date from task_history02 union select user_id,status,date from task_history03 union select user_id,status,date from task_history04 union select user_id,status,date from task_history05 union select user_id,status,date from task_history06 union select user_id,status,date from task_history07 union select user_id,status,date from task_history08 union select user_id,status,date from task_history09) c where status=2 and date > '" . $daydate . "')";

		$sql = "select e.id,e.email,e.nick,e.register_date from user e where e.points>0 and e.register_date < '" . $daydate . "' and e.id not in " . $sqlpoint . " and e.id not in " . $sqltask;
		return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();

	}

	public function getRecentPoint($yesterday) {
		$start = $yesterday . " 00:00:00";
		$end = $yesterday . " 23:59:59";
		$sql = "select
                  a.nick,
                  a.icon_path,
                  b.create_time,
                  b.reason,
                  b.point_change_num,
                  c.display_name
                from
                  user a
                  left join (
                    select
                      user_id,
                      create_time ,
                      point_change_num,reason
                    from
                      point_history00
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history01
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history02
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history03
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history04
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history05
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history06
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history07
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history08
                    union
                    select
                      user_id,
                      create_time ,point_change_num,reason
                    from
                      point_history09
                  ) b on a.id = b.user_id
                left join ad_category c on b.reason = c.id
                where create_time >= '" . $start . "'
                and create_time <= '" . $end . "'
                order by
                  abs(point_change_num) desc
                limit 100";
		return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
	}

	public function getRanking($start, $end) {
		$sql = "select
                  a.nick,
                  sum(b.point_change_num) total
                from
                  user a
                  left join (
                    select
                      user_id,
                      create_time ,
                      point_change_num
                    from
                      point_history00
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history01
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history02
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history03
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history04
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history05
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history06
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history07
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history08
                    union
                    select
                      user_id,
                      create_time ,point_change_num
                    from
                      point_history09
                  ) b on a.id = b.user_id
                where create_time >= '" . $start . "'
                and create_time <= '" . $end . "'
                and point_change_num > 0
                group by nick
                order by total desc
                limit 5
                ";
		return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
	}
}